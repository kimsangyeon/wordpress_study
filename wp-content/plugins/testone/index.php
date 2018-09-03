<?php
/*
Plugin Name: Test One
Plugin URI: http://synapsoft.co.kr
Description: A brief description of the Plugin.
Version: The Plugin's Version Number, e.g.: 1.0
Author: Name Of The Plugin Author
Author URI: http://URI_Of_The_Plugin_Author
License: A "Slug" license name e.g. GPL2
*/
function testShortCode() {
?>
    <p style="color:red">
        hello this is shortcode
    </p>
<?php
}
add_shortcode('test', 'testShortCode');

function dollyShortCode() {
    echo "Hello Dolly";
}
add_shortcode('dolly', 'dollyShortCode');

/* 이 함수는 워드프레스 페이지(사용자)가 열릴때 또는 관리자가 열릴때 특정 행동이나 첨부 등을 수행 할수 있는 행동을 추가
    추가 되는 행동은 정해져 있다
    첫번째 파라미터 wp_enqueue_scripts : 스크립트가 큐에 올라간 타이밍 페이지 상단에서 헤더부분 만들어질때 이 스크립트가 추가 된다
    두번째 파라미터는 함수를 호출 */

function addscript() {
    // wp_enqueue_script: 헤더에 스크립트를 추가 하는 함수, 스크립트 이름과 파일 주소가 필요
    // plugins_url 현재 제작하고있는 플러그인의 주소를 찾아옴
    wp_enqueue_script('', plugins_url('/js/my.js', __FILE__));
}

add_action('wp_enqueue_scripts', 'addscript');