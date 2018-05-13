<?php

class STLArea
{

    //Properties
    //----------

    //3D Object Properties
    private $b_binary;

    //Infrastructure Properties
    private $points;
    private $fstl_handle;
    private $fstl_path;
	private $triangles;
    private $flag = false;


    //Function defs
    //-------------


    //0. Contructor (PHP 5)
    /*
     * Initialises the STLStats class by passing the path to the binary .stl file.
     */
    function __construct($stl_file_path){
		$b = $this->isAscii($stl_file_path);
		if(! $b){
			//echo "BINARY STL Suspected.\n";
			$this->b_binary = TRUE;
			$this->fstl_handle = fopen($stl_file_path,"rb");    //Opens the STL file in binary mode for reading.
			$this->fstl_path = $stl_file_path;
		}else{
			//echo "ASCII STL Suspected.\n";
		}

		//init infrastructure arrays        
		$this->triangles = array();
    }


    //1. Public Functions

    /*
     * Returns the calculated Surface Area (sq. mm) of the 3D object represented in the binary STL.
     */
    public function getArea(){
        if(! $this->flag){  //boolean flag to minimize repeated surface area computation overhead
            $area = $this->calculateArea();
            $this->area = $area;
            $this->flag = true;
        }
        return $this->area;	//sq. mm
    }


    /*
     * Returns the number of trianges specified in the binary STL definition of the 3D object.
     */
    public function getTrianglesCount(){
        $tcount = $this->triangles_count;
        return $tcount;
    }


    //2. Infrastructure Functions

    /*
     * Invokes the binary file reader to read the header,
     * serially reads all the normal vector and triangular co-ordinates,
     * calls the math function to calculate surface area for each trangle,
     * sums up these areas to give the final surface of the 3D object represented in the .stl binary file.
     */
	private function calculateArea(){
		$totalArea = 0;
		//BINARY STL Area Calc.
		if($this->b_binary){
			$totbytes = filesize($this->fstl_path);
		    try{
			    $this->read_header();
			    $this->triangles_count = $this->read_triangles_count();
			    try{
	                while(ftell($this->fstl_handle) < $totbytes){
					   	 $totalArea += $this->read_triangle();
	                }
	          }
			    catch(Exception $e){
	                return $e;
	          }	
	        }
		    catch(Exception $e){
	            return $e;
	        }
	        fclose($this->fstl_handle);
		}
		//ASCII STL Area Calc.
		else{
			$k = 0;
			while(sizeof($this->triangles_data[4]) > 0){
				$totalArea += $this->read_triangle_ascii();
				$k += 1;
			}
			$this->triangles_count = $k;
		}

	    return abs($totalArea);
	}

   /*
     * Wrapper around PHP's unpack() function which decodes binary numerical data to float, int, etc types.
     * $sig specifies the type of data (i.e. integer, float, etc)
     * $l specifies number of bytes to read.
     */
    function my_unpack($sig, $l){
	    $s = fread($this->fstl_handle, $l);
        $stuff = unpack($sig, $s);
	    return $stuff;
    }

    /*
     * Appends to an array either a single var or the contents of another array.
     */
    function my_append($myarr, $mystuff){

        if(gettype($mystuff) == "array"){
            $myarr = array_merge($myarr, $mystuff);
        }else{
            $ctr = sizeof($myarr);
            $myarr[$ctr] = $mystuff;
        }
        return $myarr;
    }

    //3. Binary read functions

    /* 
     * Reads the binary header field in the STL file and offsets the file reader pointer to
     * enable reading the triangle-normal data.
     */
    function read_header(){
        //global $f;
	    fseek($this->fstl_handle, ftell($this->fstl_handle)+80);
    }

    /* 
     * Reads the binary field in the STL file which specifies the total number of triangles
     * and returns that integer.
     */
    function read_triangles_count(){
        $length = $this->my_unpack("I",4);	    
        return $length[1];
    }

    /*
     * Reads a triangle data from the binary STL and returns its surface area (sq. mm).
     * A binary STL is a representation of a 3D object as a collection of triangles and their normal vectors.
     * Its specifiction can be found here:
     * http://en.wikipedia.org/wiki/STL_(file_format)%23Binary_STL
     * This function reads the bytes of the binary STL file, decodes the data to give float XYZ co-ordinates of the trinaglular
     * vertices and the normal vector for a triangle.
     */
    function read_triangle(){
    	$n  = $this->my_unpack("f3", 12);
		$p1 = $this->my_unpack("f3", 12);
		$p2 = $this->my_unpack("f3", 12);
	    $p3 = $this->my_unpack("f3", 12);
	    $b  = $this->my_unpack("v", 2);

		$l = sizeof($this->points);
		$this->my_append($this->triangles,array($l, $l+1, $l+2));
		return $this->areaOfTriangle($p1,$p2,$p3);
    }


	//4. ASCII read functions
	
	 /*
     * Reads a triangle data from the ascii STL and returns its surfae area.
     */
    function read_triangle_ascii(){
		$p1[1] = floatval(array_pop($this->triangles_data[4]));
		$p1[2] = floatval(array_pop($this->triangles_data[5]));
		$p1[3] = floatval(array_pop($this->triangles_data[6]));

		$p2[1] = floatval(array_pop($this->triangles_data[7]));
		$p2[2] = floatval(array_pop($this->triangles_data[8]));
		$p2[3] = floatval(array_pop($this->triangles_data[9]));

		$p3[1] = floatval(array_pop($this->triangles_data[10]));
		$p3[2] = floatval(array_pop($this->triangles_data[11]));
		$p3[3] = floatval(array_pop($this->triangles_data[12]));

		$l = sizeof($this->points);
	   $this->my_append($this->triangles,array($l, $l+1, $l+2));
	   return $this->areaOfTriangle($p1,$p2,$p3);
    }


	/*
	 * Checks if the given file is an ASCII file.
	 * Populates the triangles_data array if TRUE.
	 */
	function isAscii($infilename){
		$b = FALSE;
		$facePattern =	'/facet\\s+normal\\s+([-+]?\\b(?:[0-9]*\\.)?[0-9]+(?:[eE][-+]?[0-9]+)?\\b)\\s+([-+]?\\b(?:[0-9]*\\.)?[0-9]+(?:[eE][-+]?[0-9]+)?\\b)\\s+([-+]?\\b(?:[0-9]*\\.)?[0-9]+(?:[eE][-+]?[0-9]+)?\\b)\\s+'
		 . 'outer\\s+loop\\s+'
		 . 'vertex\\s+([-+]?\\b(?:[0-9]*\\.)?[0-9]+(?:[eE][-+]?[0-9]+)?\\b)\\s+([-+]?\\b(?:[0-9]*\\.)?[0-9]+(?:[eE][-+]?[0-9]+)?\\b)\\s+([-+]?\\b(?:[0-9]*\\.)?[0-9]+(?:[eE][-+]?[0-9]+)?\\b)\\s+'
		 . 'vertex\\s+([-+]?\\b(?:[0-9]*\\.)?[0-9]+(?:[eE][-+]?[0-9]+)?\\b)\\s+([-+]?\\b(?:[0-9]*\\.)?[0-9]+(?:[eE][-+]?[0-9]+)?\\b)\\s+([-+]?\\b(?:[0-9]*\\.)?[0-9]+(?:[eE][-+]?[0-9]+)?\\b)\\s+'
		 . 'vertex\\s+([-+]?\\b(?:[0-9]*\\.)?[0-9]+(?:[eE][-+]?[0-9]+)?\\b)\\s+([-+]?\\b(?:[0-9]*\\.)?[0-9]+(?:[eE][-+]?[0-9]+)?\\b)\\s+([-+]?\\b(?:[0-9]*\\.)?[0-9]+(?:[eE][-+]?[0-9]+)?\\b)\\s+'
		 . 'endloop\\s+' . 'endfacet/';
		 #echo $facePattern;
		$fdata = file_get_contents($infilename);
		preg_match_all($facePattern, $fdata, $matches);
		if(sizeof($matches[0]) > 0){
			$b = TRUE;
			$this->triangles_data = $matches;
      }
		return $b;
	}


    //5. Math Functions

    /*
     * Returns the surface area of a triangle as determined by its 3D, XYZ co-ordinates.
     * The var $pn contains an array(x,y,z).
     */
    function areaOfTriangle($p1, $p2, $p3){
	    // Calculate triangle's side lengths
		$aside = sqrt(		pow(($p2[1] - $p1[1]),2)//(x2 - x1)
						+	pow(($p2[2] - $p1[2]),2)//(y2 - y1)
						+   pow(($p2[3] - $p1[3]),2));//(z2 - z1)

		$bside = sqrt(		pow(($p3[1] - $p2[1]),2)//(x3 - x2)
						+ 	pow(($p3[2] - $p2[2]),2)//(y3 - y2)
						+	pow(($p3[3] - $p2[3]),2));//(z3 - z2)

		$cside = sqrt(		pow(($p3[1] - $p1[1]),2)//(x3 - x1)
						+ 	pow(($p3[2] - $p1[2]),2)//(y3 - y1)
						+   pow(($p3[3] - $p1[3]),2));//(z3 - z1)

		// Calculate surface area of single triangle
		$totalsides = $aside+$bside+$cside;
		$ta = sqrt(($totalsides)/2*(($totalsides)/2-$aside) * (($totalsides)/2-$bside) * (($totalsides)/2-$cside));
		//echo $ta . "\n";
		return $ta;
    }



    //Function defs END
    //-------------------------------------------------------------------------------------------------

}



//EXAMPLE//
//=======//

/*
 * Example of using the STLArea.php class:
 *  Create an instance of the STLStats class by passing the complete path of the .stl BINARY file as 
 *    Call the getArea() function to calculate and return the surface area of the 3D object in sq. mm.
 */

/*
//$mystlpath = "40mmcube.stl";
$mystlpath = "nut-ascii.stl";
$obj = new STLStats($mystlpath);

echo "Test Results\n";
echo "--------------\n";
$a = $obj->getArea();
echo "Surface Area: " . $a . " sq. mm \n";

$tcount = $obj->getTrianglesCount();
echo "Triangles Count: " . $tcount . " triangles read\n";

echo "\n";
*/

?>
