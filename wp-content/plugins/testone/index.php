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

/* add_shortcode (shortcode 이름, 실행 함수 이름)
 * 이 함수는 워드 프레스에서 제공하는 숏 코드 기능을 제공
 * shortcode 이름으로 함수를 실행하면 결과값이 해당 부분에 표시됨
 * */
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


/* add_action
 * 이 함수는 워드프레스 페이지(사용자)가 열릴때 또는 관리자가 열릴때 특정 행동이나 첨부 등을 수행 할수 있는 행동을 추가
 * 첫번째 파라미터 wp_enqueue_scripts : 스크립트가 큐에 올라간 타이밍 페이지 상단에서 헤더부분 만들어질때 이 스크립트가 추가 된다
 * 두번째 파라미터는 함수를 호출
 * */
function addStaticFile() {
//     wp_enqueue_script: 헤더에 스크립트를 추가 하는 함수, 스크립트 이름과 파일 주소가 필요
//     plugins_url 현재 제작하고있는 플러그인의 주소를 찾아옴
//     wp_enqueue_script('', plugins_url('/js/my.js', __FILE__));
//
//     wp_enqueue_style('synap_editor_css', plugins_url('/css/synapeditor.css', __FILE__));
    wp_register_style('synap_editor_css', plugin_dir_url( __FILE__ ) . 'css/synapeditor.css');
    wp_enqueue_style('synap_editor_css');

//    wp_enqueue_script('synap_editor_js', plugins_url('/js/synapeditor.js', __FILE__));
    wp_register_script('synap_editor_js', plugin_dir_url( __FILE__ ) . 'js/synapeditor.js');
    wp_enqueue_script('synap_editor_js');
}
add_action('wp_enqueue_scripts', 'addStaticFile');


/**
 * synapeditor load 해보기
 */
function initSynapEditor() {
    ?>
        <div id="synapEditor" class="container"></div>
    <?php
    echo ("<script language=javascript> window.editor = new SynapEditor('synapEditor', {
            importAPI: `/~kimsangyeon/import.php`,
            imageUploadAPI: `/~kimsangyeon/upload.php`,
            videoUploadAPI: `/~kimsangyeon/upload.php`,
            fileUploadAPI: `/~kimsangyeon/upload.php`,
            });</script>");
}


add_shortcode('initSynapEditor', 'initSynapEditor');
