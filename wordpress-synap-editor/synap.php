<?php
/*
Plugin Name: Synap-Editor
Plugin URI: http://synapsoft.co.kr
Description: Wordpress Synap Editor Plugin
Version: The Plugin's Version Number, e.g.: 1.0
Author: kimsangyeon
Author URI: http://URI_Of_The_Plugin_Author
License: A "Slug" license name e.g. GPL2
*/
//function remove_post_default_editor() {
//    remove_post_type_support( 'post', 'editor' );
//}

function enqueue_styles() {
    wp_register_style('synap_editor_css', plugin_dir_url( __FILE__ ) . 'css/synapeditor.css');
    wp_enqueue_style('synap_editor_css');
}

function enqueue_scripts() {
    wp_register_script('synap_editor_js', plugin_dir_url( __FILE__ ) . 'js/synapeditor.js');
    wp_enqueue_script('synap_editor_js');
}

function initSynapEditor() {
    $post = get_post(get_the_ID());
    $content = apply_filters('the_content', $post->post_content);

    wp_editor($content, 'content');

    echo "<div id='editor-content' style='display: none;'>$content</div>" ;
    echo ("<script language=javascript>
            window.editor = new SynapEditor('content');
            window.editor.openHTML(document.getElementById('editor-content').innerHTML);
           </script>");
}

//add_action('init', 'remove_post_default_editor'); // plugin API init: WordPress가로드를 완료 한 후 모든 헤더가 전송되기 전에 발생합니다.
add_action('admin_enqueue_scripts', 'enqueue_styles'); // admin_enqueue_scripts : 관리 페이지에 CSS 및 / 또는 Javascript 문서 세트를로드
add_action('admin_enqueue_scripts','enqueue_scripts');
add_action('edit_form_after_title', 'initSynapEditor' ); // 타이틀 필드의 뒤에서 발생합니다.