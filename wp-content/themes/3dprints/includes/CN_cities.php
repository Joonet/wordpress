<?php 
add_filter( 'wc_city_select_cities', 'china_cities' );
/**
 * Replace XX with the country code. Instead of YYY, ZZZ use actual  state codes.
 */
function china_cities( $cities ) {
    $cities['CN'] = array(
        'CN2' => array(
            __( '市辖区', '3dprint' ),
            __( '县', '3dprint' ),
        ),
        'CN3' => array(
            __( '市辖区', '3dprint' ),
            __( '县', '3dprint' ),
        ),
        'CN4' => array(
            __( '石家庄市', '3dprint' ),
            __( '唐山市', '3dprint' ),
            __( '秦皇岛市', '3dprint' ),
            __( '邯郸市', '3dprint' ),
            __( '邢台市', '3dprint' ),
            __( '保定市', '3dprint' ),
            __( '张家口市', '3dprint' ),
            __( '承德市', '3dprint' ),
            __( '沧州市', '3dprint' ),
            __( '廊坊市', '3dprint' ),
            __( '衡水市', '3dprint' ),
        ),
        'CN5' => array(
            __( '太原市', '3dprint' ),
            __( '大同市', '3dprint' ),
            __( '阳泉市', '3dprint' ),
            __( '长治市', '3dprint' ),
            __( '晋城市', '3dprint' ),
            __( '朔州市', '3dprint' ),
            __( '晋中市', '3dprint' ),
            __( '运城市', '3dprint' ),
            __( '忻州市', '3dprint' ),
            __( '临汾市', '3dprint' ),
            __( '吕梁市', '3dprint' ),
        ),
        'CN6' => array(
            __( '呼和浩特市', '3dprint' ),
            __( '包头市', '3dprint' ),
            __( '乌海市', '3dprint' ),
            __( '赤峰市', '3dprint' ),
            __( '通辽市', '3dprint' ),
            __( '鄂尔多斯市', '3dprint' ),
            __( '呼伦贝尔市', '3dprint' ),
            __( '巴彦淖尔市', '3dprint' ),
            __( '乌兰察布市', '3dprint' ),
            __( '兴安盟', '3dprint' ),
            __( '锡林郭勒盟', '3dprint' ),
            __( '阿拉善盟', '3dprint' ),
        ),
        'CN7' => array(
            __( '沈阳市', '3dprint' ),
            __( '大连市', '3dprint' ),
            __( '鞍山市', '3dprint' ),
            __( '抚顺市', '3dprint' ),
            __( '本溪市', '3dprint' ),
            __( '丹东市', '3dprint' ),
            __( '锦州市', '3dprint' ),
            __( '营口市', '3dprint' ),
            __( '阜新市', '3dprint' ),
            __( '辽阳市', '3dprint' ),
            __( '盘锦市', '3dprint' ),
            __( '铁岭市', '3dprint' ),
            __( '朝阳市', '3dprint' ),
            __( '葫芦岛市', '3dprint' ),
        ),
        'CN8' => array(
            __( '长春市', '3dprint' ),
            __( '吉林市', '3dprint' ),
            __( '四平市', '3dprint' ),
            __( '辽源市', '3dprint' ),
            __( '通化市', '3dprint' ),
            __( '白山市', '3dprint' ),
            __( '松原市', '3dprint' ),
            __( '白城市', '3dprint' ),
            __( '延边朝鲜族自治州', '3dprint' ),
        ),
        'CN9' => array(
            __( '哈尔滨市', '3dprint' ),
            __( '齐齐哈尔市', '3dprint' ),
            __( '鸡西市', '3dprint' ),
            __( '鹤岗市', '3dprint' ),
            __( '双鸭山市', '3dprint' ),
            __( '大庆市', '3dprint' ),
            __( '伊春市', '3dprint' ),
            __( '佳木斯市', '3dprint' ),
            __( '七台河市', '3dprint' ),
            __( '牡丹江市', '3dprint' ),
            __( '黑河市', '3dprint' ),
            __( '绥化市', '3dprint' ),
            __( '大兴安岭地区', '3dprint' ),
        ),
        'CN10' => array(
            __( '市辖区', '3dprint' ),
            __( '县', '3dprint' ),
        ),
        'CN11' => array(
            __( '南京市', '3dprint' ),
            __( '无锡市', '3dprint' ),
            __( '徐州市', '3dprint' ),
            __( '常州市', '3dprint' ),
            __( '苏州市', '3dprint' ),
            __( '南通市', '3dprint' ),
            __( '连云港市', '3dprint' ),
            __( '淮安市', '3dprint' ),
            __( '盐城市', '3dprint' ),
            __( '扬州市', '3dprint' ),
            __( '镇江市', '3dprint' ),
            __( '泰州市', '3dprint' ),
            __( '宿迁市', '3dprint' ),
        ),
        'CN12' => array(
            __( '杭州市', '3dprint' ),
            __( '宁波市', '3dprint' ),
            __( '温州市', '3dprint' ),
            __( '嘉兴市', '3dprint' ),
            __( '湖州市', '3dprint' ),
            __( '绍兴市', '3dprint' ),
            __( '金华市', '3dprint' ),
            __( '衢州市', '3dprint' ),
            __( '舟山市', '3dprint' ),
            __( '台州市', '3dprint' ),
            __( '丽水市', '3dprint' ),
        ),
        'CN13' => array(
            __( '合肥市', '3dprint' ),
            __( '芜湖市', '3dprint' ),
            __( '蚌埠市', '3dprint' ),
            __( '淮南市', '3dprint' ),
            __( '马鞍山市', '3dprint' ),
            __( '淮北市', '3dprint' ),
            __( '铜陵市', '3dprint' ),
            __( '安庆市', '3dprint' ),
            __( '黄山市', '3dprint' ),
            __( '滁州市', '3dprint' ),
            __( '阜阳市', '3dprint' ),
            __( '宿州市', '3dprint' ),
            __( '巢湖市', '3dprint' ),
            __( '六安市', '3dprint' ),
            __( '亳州市', '3dprint' ),
            __( '池州市', '3dprint' ),
            __( '宣城市', '3dprint' ),
        ),
        'CN14' => array(
            __( '福州市', '3dprint' ),
            __( '厦门市', '3dprint' ),
            __( '莆田市', '3dprint' ),
            __( '三明市', '3dprint' ),
            __( '泉州市', '3dprint' ),
            __( '漳州市', '3dprint' ),
            __( '南平市', '3dprint' ),
            __( '龙岩市', '3dprint' ),
            __( '宁德市', '3dprint' ),
        ),
        'CN15' => array(
            __( '南昌市', '3dprint' ),
            __( '景德镇市', '3dprint' ),
            __( '萍乡市', '3dprint' ),
            __( '九江市', '3dprint' ),
            __( '新余市', '3dprint' ),
            __( '鹰潭市', '3dprint' ),
            __( '赣州市', '3dprint' ),
            __( '吉安市', '3dprint' ),
            __( '宜春市', '3dprint' ),
            __( '抚州市', '3dprint' ),
            __( '上饶市', '3dprint' ),
        ),
        'CN16' => array(
            __( '济南市', '3dprint' ),
            __( '青岛市', '3dprint' ),
            __( '淄博市', '3dprint' ),
            __( '枣庄市', '3dprint' ),
            __( '东营市', '3dprint' ),
            __( '烟台市', '3dprint' ),
            __( '潍坊市', '3dprint' ),
            __( '济宁市', '3dprint' ),
            __( '泰安市', '3dprint' ),
            __( '威海市', '3dprint' ),
            __( '日照市', '3dprint' ),
            __( '莱芜市', '3dprint' ),
            __( '临沂市', '3dprint' ),
            __( '德州市', '3dprint' ),
            __( '聊城市', '3dprint' ),
            __( '滨州市', '3dprint' ),
            __( '荷泽市', '3dprint' ),
        ),
        'CN17' => array(
            __( '郑州市', '3dprint' ),
            __( '开封市', '3dprint' ),
            __( '洛阳市', '3dprint' ),
            __( '平顶山市', '3dprint' ),
            __( '安阳市', '3dprint' ),
            __( '鹤壁市', '3dprint' ),
            __( '新乡市', '3dprint' ),
            __( '焦作市', '3dprint' ),
            __( '濮阳市', '3dprint' ),
            __( '许昌市', '3dprint' ),
            __( '漯河市', '3dprint' ),
            __( '三门峡市', '3dprint' ),
            __( '南阳市', '3dprint' ),
            __( '商丘市', '3dprint' ),
            __( '信阳市', '3dprint' ),
            __( '周口市', '3dprint' ),
            __( '驻马店市', '3dprint' ),
        ),
        'CN18' => array(
            __( '武汉市', '3dprint' ),
            __( '黄石市', '3dprint' ),
            __( '十堰市', '3dprint' ),
            __( '宜昌市', '3dprint' ),
            __( '襄樊市', '3dprint' ),
            __( '鄂州市', '3dprint' ),
            __( '荆门市', '3dprint' ),
            __( '孝感市', '3dprint' ),
            __( '荆州市', '3dprint' ),
            __( '黄冈市', '3dprint' ),
            __( '咸宁市', '3dprint' ),
            __( '随州市', '3dprint' ),
            __( '恩施土家族苗族自治州', '3dprint' ),
            __( '省直辖行政单位', '3dprint' ),
        ),
        'CN19' => array(
            __( '长沙市', '3dprint' ),
            __( '株洲市', '3dprint' ),
            __( '湘潭市', '3dprint' ),
            __( '衡阳市', '3dprint' ),
            __( '邵阳市', '3dprint' ),
            __( '岳阳市', '3dprint' ),
            __( '常德市', '3dprint' ),
            __( '张家界市', '3dprint' ),
            __( '益阳市', '3dprint' ),
            __( '郴州市', '3dprint' ),
            __( '永州市', '3dprint' ),
            __( '怀化市', '3dprint' ),
            __( '娄底市', '3dprint' ),
            __( '湘西土家族苗族自治州', '3dprint' ),
        ),
        'CN20' => array(
            __( '广州市', '3dprint' ),
            __( '韶关市', '3dprint' ),
            __( '深圳市', '3dprint' ),
            __( '珠海市', '3dprint' ),
            __( '汕头市', '3dprint' ),
            __( '佛山市', '3dprint' ),
            __( '江门市', '3dprint' ),
            __( '湛江市', '3dprint' ),
            __( '茂名市', '3dprint' ),
            __( '肇庆市', '3dprint' ),
            __( '惠州市', '3dprint' ),
            __( '梅州市', '3dprint' ),
            __( '汕尾市', '3dprint' ),
            __( '河源市', '3dprint' ),
            __( '阳江市', '3dprint' ),
            __( '清远市', '3dprint' ),
            __( '东莞市', '3dprint' ),
            __( '中山市', '3dprint' ),
            __( '潮州市', '3dprint' ),
            __( '揭阳市', '3dprint' ),
           __(  '云浮市', '3dprint' ),
        ),
        'CN21' => array(
            __( '南宁市', '3dprint' ),
            __( '柳州市', '3dprint' ),
            __( '桂林市', '3dprint' ),
            __( '梧州市', '3dprint' ),
            __( '北海市', '3dprint' ),
            __( '防城港市', '3dprint' ),
            __( '钦州市', '3dprint' ),
            __( '贵港市', '3dprint' ),
            __( '玉林市', '3dprint' ),
            __( '百色市', '3dprint' ),
            __( '贺州市', '3dprint' ),
            __( '河池市', '3dprint' ),
            __( '来宾市', '3dprint' ),
            __( '崇左市', '3dprint' ),
        ),
        'CN22' => array(
            __( '海口市', '3dprint' ),
            __( '三亚市', '3dprint' ),
            __( '省直辖县级行政单位', '3dprint' ),
        ),
        'CN23' => array(
            __( '市辖区', '3dprint' ),
            __( '县', '3dprint' ),
            __( '市', '3dprint' ),
        ),
        'CN24' => array(
            __( '成都市', '3dprint' ),
            __( '自贡市', '3dprint' ),
            __( '攀枝花市', '3dprint' ),
            __( '泸州市', '3dprint' ),
            __( '德阳市', '3dprint' ),
            __( '绵阳市', '3dprint' ),
            __( '广元市', '3dprint' ),
            __( '遂宁市', '3dprint' ),
            __( '内江市', '3dprint' ),
            __( '乐山市', '3dprint' ),
            __( '南充市', '3dprint' ),
            __( '眉山市', '3dprint' ),
            __( '宜宾市', '3dprint' ),
            __( '广安市', '3dprint' ),
            __( '达州市', '3dprint' ),
            __( '雅安市', '3dprint' ),
            __( '巴中市', '3dprint' ),
            __( '资阳市', '3dprint' ),
            __( '阿坝藏族羌族自治州', '3dprint' ),
            __( '甘孜藏族自治州', '3dprint' ),
            __( '凉山彝族自治州', '3dprint' ),
        ),
        'CN25' => array(
            __( '贵阳市', '3dprint' ),
            __( '六盘水市', '3dprint' ),
            __( '遵义市', '3dprint' ),
            __( '安顺市', '3dprint' ),
            __( '铜仁地区', '3dprint' ),
            __( '黔西南布依族苗族自治州', '3dprint' ),
            __( '毕节地区', '3dprint' ),
            __( '黔东南苗族侗族自治州', '3dprint' ),
            __( '黔南布依族苗族自治州', '3dprint' ),
        ),
        'CN1' => array(
            __( '昆明市', '3dprint' ),
            __( '曲靖市', '3dprint' ),
            __( '玉溪市', '3dprint' ),
            __( '保山市', '3dprint' ),
            __( '昭通市', '3dprint' ),
            __( '丽江市', '3dprint' ),
            __( '思茅市', '3dprint' ),
            __( '临沧市', '3dprint' ),
            __( '楚雄彝族自治州', '3dprint' ),
            __( '红河哈尼族彝族自治州', '3dprint' ),
            __( '文山壮族苗族自治州', '3dprint' ),
            __( '西双版纳傣族自治州', '3dprint' ),
            __( '大理白族自治州', '3dprint' ),
            __( '德宏傣族景颇族自治州', '3dprint' ),
            __( '怒江傈僳族自治州', '3dprint' ),
            __( '迪庆藏族自治州', '3dprint' ),
        ),
        'CN31' => array(
            __( '拉萨市', '3dprint' ),
            __( '昌都地区', '3dprint' ),
            __( '山南地区', '3dprint' ),
            __( '日喀则地区', '3dprint' ),
            __( '那曲地区', '3dprint' ),
            __( '阿里地区', '3dprint' ),
            __( '林芝地区', '3dprint' ),
        ),
        'CN26' => array(
            __( '西安市', '3dprint' ),
            __( '铜川市', '3dprint' ),
            __( '宝鸡市', '3dprint' ),
            __( '咸阳市', '3dprint' ),
            __( '渭南市', '3dprint' ),
            __( '延安市', '3dprint' ),
            __( '汉中市', '3dprint' ),
            __( '榆林市', '3dprint' ),
            __( '安康市', '3dprint' ),
            __( '商洛市', '3dprint' ),
        ),
        'CN27' => array(
            __( '兰州市', '3dprint' ),
            __( '嘉峪关市', '3dprint' ),
            __( '金昌市', '3dprint' ),
            __( '白银市', '3dprint' ),
            __( '天水市', '3dprint' ),
            __( '武威市', '3dprint' ),
            __( '张掖市', '3dprint' ),
            __( '平凉市', '3dprint' ),
            __( '酒泉市', '3dprint' ),
            __( '庆阳市', '3dprint' ),
            __( '定西市', '3dprint' ),
            __( '陇南市', '3dprint' ),
            __( '临夏回族自治州', '3dprint' ),
            __( '甘南藏族自治州', '3dprint' ),
        ),
        'CN28' => array(
            __( '西宁市', '3dprint' ),
            __( '海东地区', '3dprint' ),
            __( '海北藏族自治州', '3dprint' ),
            __( '黄南藏族自治州', '3dprint' ),
            __( '海南藏族自治州', '3dprint' ),
            __( '果洛藏族自治州', '3dprint' ),
            __( '玉树藏族自治州', '3dprint' ),
            __( '海西蒙古族藏族自治州', '3dprint' ),
        ),
        'CN29' => array(
            __( '银川市', '3dprint' ),
            __( '石嘴山市', '3dprint' ),
            __( '吴忠市', '3dprint' ),
            __( '固原市', '3dprint' ),
            __( '中卫市', '3dprint' ),
        ),
        'CN30' => false,
        'CN32' => array(
            __( '乌鲁木齐市', '3dprint' ),
            __( '克拉玛依市', '3dprint' ),
            __( '吐鲁番地区', '3dprint' ),
            __( '哈密地区', '3dprint' ),
            __( '昌吉回族自治州', '3dprint' ),
            __( '博尔塔拉蒙古自治州', '3dprint' ),
            __( '巴音郭楞蒙古自治州', '3dprint' ),
            __( '阿克苏地区', '3dprint' ),
            __( '克孜勒苏柯尔克孜自治州', '3dprint' ),
            __( '喀什地区', '3dprint' ),
            __( '和田地区', '3dprint' ),
            __( '伊犁哈萨克自治州', '3dprint' ),
            __( '塔城地区', '3dprint' ),
            __( '阿勒泰地区', '3dprint' ),
            __( '省直辖行政单位', '3dprint' ),
        ),
        'CN33' => false,
        'CN34' => false,        
    );
    return $cities;
}