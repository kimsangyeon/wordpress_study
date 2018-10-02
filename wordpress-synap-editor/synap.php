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
$file_server_path = realpath(__FILE__);
$server_path = str_replace(basename(__FILE__), "", $file_server_path);

$GLOBALS['CONVERT_SERVER'] = "http://synapeditor.iptime.org:7419/convertDocToPb";
$GLOBALS['ZIP_FILE_PATH'] = $server_path."uploadFile/doc/zip/";
$GLOBALS['UNZIP_FILE_PATH'] = $server_path."uploadFile/doc/unzip/";

function synap_import_file() {
    $valid_formats = array("doc", "docx");
    $data = array();
    $data['success'] = false;

    $name = $_FILES['file']['name'];
    $tmp_name = $_FILES['file']['tmp_name'];
    $type = $_FILES['file']['type'];
    $size = $_FILES['file']['size'];

    if (strlen($name)) {
        list($txt, $ext) = explode(".", $name);

        if (in_array(strtolower($ext),$valid_formats)) {
            $result = getConvertToPbData($tmp_name, $type, $name, $size);
            $fp = fopen($GLOBALS['ZIP_FILE_PATH'] . "document.word.pb.zip", 'w');
            fwrite($fp, $result);
            fclose($fp);

            ob_clean();

            $pbFilePath = unzipFile();
            $serializedData = getSerializePbData($pbFilePath);

            $data['serializedData'] = $serializedData;
            $data['success'] = true;

        } else {
            $data['error'] = "Invalid file format..";
        }
    } else {
        $data['error'] = "Please select file..!";
    }


    echo stripslashes( json_encode($data) );

    exit();
    wp_die();
}


function getConvertToPbData($tmp_name, $type, $name, $size) {
    $headers = array("Content-Type:multipart/form-data");
    $curl_file = curl_file_create($tmp_name, $type, $name);
    $post_fields = array(
        'file' => $curl_file);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);
    curl_setopt($ch, CURLOPT_INFILESIZE, $size);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_URL, $GLOBALS['CONVERT_SERVER']);

    $response = curl_exec($ch);
    $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    $header = substr($response, 0, $header_size);

    curl_close ($ch);

    return substr($response, $header_size);
}

function unzipFile() {
    $zip = new ZipArchive;
    $pbFilePath = $GLOBALS['UNZIP_FILE_PATH']."document.word.pb";
    $res = $zip->open($GLOBALS['ZIP_FILE_PATH']."document.word.pb.zip");

    if ($res === TRUE) {
        $zip->extractTo($GLOBALS['UNZIP_FILE_PATH']);
        $zip->close();
    } else {
        echo 'fail unzip file ...';
    }

    return $pbFilePath;
}

function getSerializePbData($pbFilePath) {
    $serializedData = array();

    $fb = fopen($pbFilePath, 'rb');
    if ($fb) {
        $contents = zlib_decode(stream_get_contents($fb, -1, 16));
        $data = unpack('C*', $contents);

        for ($i = 1; $i < sizeof($data); $i++) {
            array_push($serializedData, $data[$i] & 0xFF);
        }
    }

    fclose($fb);

    return $serializedData;
}

class SynapEditor
{
    /**
     * Holds the values to be used in the fields callbacks
     */
    private $options;

    /**
     * Start up
     */
    public function __construct()
    {
        add_action('init', array( $this, 'remove_post_default_editor')); // plugin API init: WordPress가로드를 완료 한 후 모든 헤더가 전송되기 전에 발생합니다.
        add_action('admin_enqueue_scripts', array( $this, 'enqueue_styles')); // admin_enqueue_scripts : 관리 페이지에 CSS 및 / 또는 Javascript 문서 세트를로드
        add_action('admin_enqueue_scripts', array( $this, 'enqueue_scripts'));

        add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
        add_action( 'admin_init', array( $this, 'page_init' ) );

        // 타이틀 필드의 뒤에서 발생합니다.
        add_action('edit_form_after_title', array( $this, 'initSynapEditor'));

        // 워드프레스 2.8 이후로 wp_ajax_(action) 과 같은 hook이 생겼습니다.
        add_action('wp_ajax_synap_upload_files', array( $this, 'synap_upload_files'));

        // 로그인 하지 않은 사용자들에게 실행되는 ajax 액션 입니다.
        add_action('wp_ajax_nopriv_synap_upload_files', array( $this, 'synap_upload_files'));

        add_action('wp_ajax_synap_import_file', 'synap_import_file');
    }

    /**
     * Add options page
     */
    public function add_plugin_page()
    {
        // This page will be under "Settings"
        add_options_page(
            'Settings Admin',
            'SynapEditor Settings',
            'manage_options',
            'my-setting-admin',
            array( $this, 'create_admin_page' )
        );
    }

    /**
     * Options page callback
     */
    public function create_admin_page()
    {
        // Set class property
        $this->options = get_option( 'synap_option' );
        ?>
        <div class="wrap">
            <h1>My Settings</h1>
            <form method="post" action="options.php">
                <?php
                // This prints out all hidden setting fields
                settings_fields( 'synap_option_group' );
                do_settings_sections( 'my-setting-admin' );
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }

    public function remove_post_default_editor() {
        remove_post_type_support('post', 'editor');
    }

    /**
     * Register and add settings
     */
    public function page_init()
    {
        register_setting(
            'synap_option_group', // Option group
            'synap_option', // Option name
            array( $this, 'sanitize' ) // Sanitize
        );

        add_settings_section(
            'setting_section_id', // ID
            'My Custom Settings', // Title
            array( $this, 'print_section_info' ), // Callback
            'my-setting-admin' // Page
        );

        add_settings_field(
            'editor.size.width', // Width
            'Editor Width',
            array( $this, 'editor_width_callback' ), // Callback
            'my-setting-admin', // Page
            'setting_section_id' // Section
        );

        add_settings_field(
            'editor.size.height',
            'Editor Height',
            array( $this, 'editor_height_callback' ),
            'my-setting-admin',
            'setting_section_id'
        );
    }

    public function initSynapEditor() {
        $post = get_post(get_the_ID());
        $content = apply_filters('the_content', $post->post_content);
        $this->options = get_option( 'synap_option' );
        $width = $this->options['editor.size.width'];
        $height = $this->options['editor.size.height'];

        echo "<textarea id='content' name='content' style='display: none;'></textarea>" ;
        echo "<div id='editor-content' style='display: none;'>$content</div>" ;
        echo ("<script language=javascript>
            window.onload = function () {
                if (SynapEditor) {
                    const options = {};
                                        
                    if (Number.isInteger($width)) {
                        options['editor.size.width'] = $width + 'px';
                    }      
                    if (Number.isInteger($height)) {
                        options['editor.size.height'] = $height + 'px';
                    }

                    window.editor = new SynapEditor('content', options);
                    window.editor.openHTML(document.getElementById('editor-content').innerHTML);
                }
            }
           </script>");
    }

    /**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     */
    public function sanitize( $input )
    {
        $new_input = array();
        if( isset( $input['editor.size.width'] ) )
            $new_input['editor.size.width'] = absint( $input['editor.size.width'] );

        if( isset( $input['editor.size.height'] ) )
            $new_input['editor.size.height'] = sanitize_text_field( $input['editor.size.height'] );

        return $new_input;
    }

    /**
     * Print the Section text
     */
    public function print_section_info()
    {
        print 'Enter your settings below:';
    }

    /**
     * Get the settings option array and print one of its values
     */
    public function editor_width_callback()
    {
        printf(
            '<input type="text" id="editor.size.width" name="synap_option[editor.size.width]" value="%s" />',
            isset( $this->options['editor.size.width'] ) ? esc_attr( $this->options['editor.size.width']) : ''
        );
    }

    /**
     * Get the settings option array and print one of its values
     */
    public function editor_height_callback()
    {
        printf(
            '<input type="text" id="editor.size.height" name="synap_option[editor.size.height]" value="%s" />',
            isset( $this->options['editor.size.height'] ) ? esc_attr( $this->options['editor.size.height']) : ''
        );
    }

    public function enqueue_styles() {
        wp_register_style('synap_editor_css', plugin_dir_url( __FILE__ ) . 'css/synapeditor.css');
        wp_enqueue_style('synap_editor_css');
    }

    public function enqueue_scripts() {
        wp_register_script('synap_editor_js', plugin_dir_url( __FILE__ ) . 'js/synapeditor.js');
        wp_enqueue_script('synap_editor_js');
    }

    public function synap_upload_files() {
        if ($_FILES) {

            // Let WordPress handle the upload.
            $attachment_id = media_handle_upload( 'file', 0 );

            if ( is_wp_error( $attachment_id ) ) {
                // There was an error uploading the image.
            } else {
                // The image was uploaded successfully!
                $file_path      = wp_get_attachment_url( $attachment_id );
                $response       = new StdClass;
                $response->uploadPath = $file_path;

                echo stripslashes( json_encode( $response ) );
            }
        }
        exit();
        wp_die();
    }
}

$synapEditor = new SynapEditor();