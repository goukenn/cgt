<?php
//----------------------------------------------------------------------------------------------
//file. igk_config.php
//Description: Represent configuration and constants settings
//AUTHOR: C.A.D. BONDJE DOUE
//create: 01/01/2013
//----------------------------------------------------------------------------------------------

//IGK_NO_WEB_REDIRECT to disable redirection
//IGK_CONF_CONNECT enable config connection for debugging purpose

define("IGK_DEFAULT_LANG_FOLDER", dirname(__FILE__)."/Default/Lang/");
define("IGK_SESSION_FILE_PREFIX", "sess_");
define("IGK_APP_SESSION_KEY","igk");
define("IGK_COMPONENT_NAMESFILE", IGK_LIB_DIR."/Data/References/Components/Inc/names.pinc");


// if (!defined("IGK_APP_DIR") && !defined("IGK_FRAMEWORK_ATOMIC")){
	// define("IGK_FRAMEWORK_ATOMIC", 1);//define app framework atomin
// }


// define("IGK_TRACE", 1);			// Trace igk_wln output

// define("IGK_TRACE_CLEAN", 1); //used to clear ob buffer when igk_show_trace is invoked
// define("IGK_DEBUG", 1);
// define("IGK_CACHE_REQUIRE", 1); //set if when rendering document we must render it as cache document
// define("IGK_PADDING_HEADER", 1); //add extra padding to document after !Doctype
// define("IGK_WRITE_LOG", 1);
define("IGK_NODESTROY_ON_FATAL", 1); //for debugging stop session destroy on fatal error
// define("IGK_JS_TRIMSCRIPTS", 1); //force trim script
// define("IGK_NO_SESSION", 1); //disable platform session start
// define("IGK_NO_LIB_EXTENSION", 1); //disable library extension
// define("IGK_UTEST", 1); //activate the utesting mode
//define("IGK_NO_CACHE_LIB", 1); //used to deactivate the library cache
//indicate usage of sitemap
// define("IGK_SITEMAP_REQUEST", 1);
// define("IGK_NO_SESSION_BUTTON", 1);
define("IGK_LOCAL_DEBUGGING", 1); //change to 1 to allow php view the fatal error handling
define("IGK_DIE_DEFAULT_MSG", "Die call");

define("IGK_KEY_APP", "igk");
define("IGK_KEY_GLOBALVARS", "sys://igk/globalvars");//store global variables

define("IGK_KEY_FORCEVIEW", "sys://igk/forceview");//0 or 1 to indicate that the method force view has been called
define("IGK_KEY_VIEW_FORCED","sys://igk/viewforced");//0 or 1 to indicate that method forceview finish the called
define("IGK_KEY_TOOLS", "sys://igk/tools"); //in environement store registered tools 
define("IGK_KEY_PARAM_SESSION_START_AT", "sys://igk/create-at"); //stored in session param get time where a web client start a new session
define("IGK_KEY_CSS_NOCLEAR", "sys://css/noclear"); //in environement store registered tools 
define("IGK_KEY_APP_SELECTED_USER_PROFILE", "app://selectedUserProfile"); //in environement store registered tools 

define("IGK_KEY_DOC_NO_STORE_RENDERING", "sys://document/NOSTORERENDERING") ;//# param to set to document to 1 if the document must not store to last rendering document. in IGKHtmlDoc::SetLastRenderingDocument #/ 
define("IGK_ROOT_SERVER", "http://www.igkdev.com");

define("IGK_ENV_NO_AJX_TEST", "sys://env/no_ajx_test"); //diseable ajx testing for the net calling of igk_is_ajx_demand
define("IGK_ENV_HTML_COMPONENTS", "sys://env/html/components"); //used to register html custom html component
define("IGK_ENV_HTML_NS_PREFIX", "sys://env/html/prefix"); //used to register html custom prefix
define("IGK_ENV_REQUEST_METHOD", "sys://request_method"); //used to register html custom prefix

//sytem envent
define("IGK_BASE_EVENT", 0xe00);
define("IGK_ENV_SETTING_CHANGED", IGK_BASE_EVENT + 1 );
define("IGK_ENV_APP_INIT", IGK_BASE_EVENT + 2 );
define("IGK_ENV_NEW_DOC_CREATED", IGK_BASE_EVENT + 3);
define("IGK_ENV_LANG_CHANGED",  IGK_BASE_EVENT + 4);
define("IGK_ENV_THEME_CHANGED", IGK_BASE_EVENT + 5);




define("IGK_ENV_APP_CONTEXT", "sys://app/context"); //running context state. (initialize| starting | running). use igk_current_context to get the context
define("IGK_ENV_NO_TRACE_KEY", "sys://no_trace"); //disable the line tracing
define("IGK_ENV_GLOBAL_SETTING", "sys://global/settings"); //global setting keys
define("IGK_ENV_CTRL_VIEW", "sys://igk_ctrl_view/mode"); //store the view mode in igk_ctrl_view  function 
define("IGK_ENV_INVOKE_ARGS", "sys://igk/invokeuri/args"); //param store for invoke uri param state
define("IGK_ENV_COMPONENT_DISPLAY_NAMES_KEY", "sys://components/displaynames"); //displays names of the components
define("IGK_ENV_COMPONENT_REFDIRS_KEY", "sys://components/refdirs"); //store reference directory for declared component in a file.

define("IGK_ENV_CALLBACK_KEYS","sys://env/callbacks");


define("IGK_LAST_EVAL_KEY", "sys://lasteval");
define("IGK_ENV_WIDGETS_KEY", "sys://widgets");
define("IGK_CACHE_HTML", ".enable-html");
//
//sql config info
//
define("IGK_SQL_DEFAULT_DATE_TIME", "0001-01-01 00:00:00");
define("IGK_SQL_DEFAULT_TIME", "00:00:00");

define("IGK_CSS_DEFAULT_STYLE_FUNC_KEY", "sys://css/function/defaultStyle");
define("IGK_CSS_MEDIA_TYPE_CLASS", ".igk-media-type:before");//", "sys://css/function/defaultStyle");
 

define("IGK_FC_GETVALUE", "getValue");
define("IGK_GLOBAL_EVENT", "@global");
define("IGK_FUNC_KEY", "func");



//start flag
define("IGK_VIEW_MODE_FLAG", 0x0001);
define("IGK_FIRSTUSE_FLAG", 0x0002);
define("IGK_ISINIT_FLAG", 0x0003);

//base config
define("IGK_CONFIG_FLAG", 0xA00);			//parameter store in application
define("IGK_CREATE_AT", IGK_CONFIG_FLAG+1);
define("IGK_SESSION_ID", IGK_CONFIG_FLAG+2);
define("IGK_SUBDOMAIN_CTRL", IGK_CONFIG_FLAG+3);
define("IGK_INVOKE_URI_CTRL", IGK_CONFIG_FLAG+4); //invokedUriController

//node flags
define("IGK_NODE_FLAG", 0xF0);
define("IGK_ISVISIBLE_FLAG", IGK_NODE_FLAG+1);
define("IGK_AUTODINDEX_FLAG", IGK_NODE_FLAG+2);
define("IGK_ZINDEX_FLAG", IGK_NODE_FLAG+3);
define("IGK_ISLOADING_FLAG", IGK_NODE_FLAG+4);
define("IGK_LOADINGCONTEXT_FLAG", IGK_NODE_FLAG+5);//on loading context
define("IGK_NODETYPE_FLAG", IGK_NODE_FLAG+6);//type, class|function | c|f
define("IGK_NODETYPENAME_FLAG", IGK_NODE_FLAG+7);//class type. if null set use the current class or set
define("IGK_NODETAG_FLAG", IGK_NODE_FLAG+8); //tagname
define("IGK_NODECONTENT_FLAG", IGK_NODE_FLAG+9); //content flag 0xF9
define("IGK_SORTREQUIRED_FLAG", IGK_NODE_FLAG+10); //
define("IGK_PARENTHOST_FLAG", IGK_NODE_FLAG+11); //
define("IGK_PREVIOUS_CIBLING_FLAG", IGK_NODE_FLAG+12); //
define("IGK_DESC_FLAG", IGK_NODE_FLAG+13); //node custom description
define("IGK_STYLE_FLAG", IGK_NODE_FLAG+14); //node custom description
define("IGK_ISDISPOSING_FLAG", IGK_NODE_FLAG+15); //
define("IGK_ATTACHDISPOSE_FLAG", IGK_NODE_FLAG+16); //
define("IGK_ATTACHCHILD_FLAG", IGK_NODE_FLAG+17); //
define("IGK_NSFC_FLAG", IGK_NODE_FLAG+18); //
define("IGK_CALLBACK_FLAG", IGK_NODE_FLAG+19); //
define("IGK_PARAMS_FLAG", IGK_NODE_FLAG+20); //
define("IGK_PARENT_FLAG", IGK_NODE_FLAG+21); //
define("IGK_CHILDS_FLAG", IGK_NODE_FLAG+22); //childs collection
define("IGK_ATTRS_FLAG", IGK_NODE_FLAG+23); //node attribute
define("IGK_DEFINEDNS_FLAG", IGK_NODE_FLAG+24); //node attribute

define("IGK_NODE_CREATE_ARGS_FLAG", IGK_NODE_FLAG+25);
//document default parameter
define("IGK_DOC_ID_PARAM", IGK_NODE_FLAG+26); //param for document that store the id - string or controller are allowed 
define("IGK_COMPONENT_ID_PARAM", IGK_NODE_FLAG+27); //id set by IGKComponentManagerCtrl on component

 
define("IGK_KEY_DOCUMENTS", IGK_NODE_FLAG+40);//store documents
define("IGK_KEY_LASTDOC", IGK_NODE_FLAG+41);//store documents
define("IGK_KEY_LAST_RENDERED_DOC", IGK_NODE_FLAG+42); //in environement store registered tools 
define("IGK_KEY_SYSDB_CTRL", IGK_NODE_FLAG +43);


 
 define("IGK_CTRL_TG_NODE", 0xF0);
 
 define("IGK_COMPONENT_TYPE_FUNCTION", "f");
 define("IGK_COMPONENT_TYPE_CLASS", "c");
 
define("IGK_CTRL_TABLE_INFO_KEY", "sys://ctrl/tabinfokey");//used to store table info in ctrl params
// $this->reg_media("(max-width:315px)", IGKHtmlDocThemeMediaType::XSM_MEDIA, "xsm");
// $this->reg_media("(max-width:710px)", IGKHtmlDocThemeMediaType::SM_MEDIA,"sm");
// $this->reg_media("(min-width:1024px)", IGKHtmlDocThemeMediaType::LG_MEDIA, "lg");
// $this->reg_media("(min-width:1200px)", IGKHtmlDocThemeMediaType::XLG_MEDIA, "xlg");
// $this->reg_media("(min-width:1600px)", IGKHtmlDocThemeMediaType::XXLG_MEDIA,"xxlg");

//CSS DEF		
			
define("IGK_CSS_XSM_SCREEN", 320);
define("IGK_CSS_SM_SCREEN", 710);
define("IGK_CSS_LG_SCREEN", 1024);
define("IGK_CSS_XLG_SCREEN", 1200);
define("IGK_CSS_XXLG_SCREEN", 1600);
 

define("IGK_CSS_CTN_LG_SIZE", 844);
define("IGK_CSS_CTN_XLG_SIZE", 1280);
define("IGK_CSS_CTN_XXLG_SIZE", 1580);
 
define("IGK_CSS_TEMP_FILES_KEY", "sys://css/temp/files");
define("IGK_AJX_BINDSTYLES", "sys://css/ajx/temp/files");
 
//SYS KEY
define("IGK_COMPONENT_ID_KEY", "sys://component/id"); // set the param of a component id
 
//define("IGK_ROOT_SERVER", "http://localhost/igkdev");
define("IGK_ENCODINGTYPE", "text/html; charset=utf-8");
define("IGK_SERVERNAME", "IGKDEV"); 
define("IGK_STR_EMPTY","");
define("IGK_MAX_CONFIG_PWD_LENGHT", 4);
define("IGK_DEFAULT_FOLDER_MASK", 0755);
define("IGK_DEFAULT_FILE_MASK", 0755);
define("IGK_LF","\n");//line field
define("IGK_CLF","\r\n");//line field
define("IGK_DATA_FOLDER", "Data"); //data folder
define("IGK_CONF_DATA", IGK_DATA_FOLDER."/configs.csv"); //config data file
define("IGK_CHANGE_CONF_DATA",IGK_DATA_FOLDER."/changes.csv");
define("IGK_UPLOAD_DATA", "Data/upload.csv"); //upload data file
define("IGK_USER_LOGIN", "bondje.doue@igkdev.com"); //user login for debug
define("IGK_DB_PREFIX_TABLE_NAME", "IGK_DB_P_TABLE");
define("IGK_CTRL_PARAM_CSS_INIT", "css-init");
define("IGK_DATABINDING_RESPONSE_NAME", "IGK_DATABINDING_RESPONSE_NAME");
define("IGK_ADD_PREFIX","add");
define("IGK_MENU_CONF_DATA",IGK_DATA_FOLDER."/menuconf.csv");
define("IGK_MENUS_REGEX", "/menu(?P<name>(.)+)conf.csv/i");
define("IGK_IDENTIFIER_RX", "([a-z]|[_]+[a-z0-9])([a-z0-9_]*)");
define("IGK_XML_IDENTIFIER_RX", "([a-z]+:)*([a-z]|[_]+[a-z0-9])([a-z0-9_-]*)");
define("IGK_ISIDENTIFIER_REGEX", "/^".IGK_IDENTIFIER_RX."$/i");
define("IGK_IS_NS_IDENTIFIER_REGEX", "/^((_*[0-9a-z][0-9a-z_]*)\\\\)+(_*[0-9a-z][0-9a-z_]*)$/i");//php namespace checker regex
// define("IGK_IS_FQN_NS_REGEX", "/^((((_*[a-z])|(_+[0-9a-z]))[0-9a-z_]*)\.)*(((_*[a-z])|(_+[0-9a-z]))[0-9a-z_]*)$/i");
define("IGK_FQN_NS_RX", "((".IGK_IDENTIFIER_RX.")\.)*(".IGK_IDENTIFIER_RX.")");
define("IGK_IS_FQN_NS_REGEX", "/^((".IGK_IDENTIFIER_RX.")\.)*(".IGK_IDENTIFIER_RX.")$/i");
define("IGK_TAGNAME_REGEX", "[0-9a-z\-\:]+");
define("IGK_TAGNAME_CHAR_REGEX", "[0-9a-z\-\:_\.]");
define("IGK_NAME_SPACE_REGEX", "/[a-z_][a-z0-9_\.]+/i");
define("IGK_HOME", "home");


define("IGK_ENV_PARAM_KEY", "sys://EnvParam");
define("IGK_ENV_PAGEFOLDER_CHANGED_KEY", "sys://current_page_folder_changed");
define("IGK_ENV_NO_COOKIE_KEY", "sys://no_cookie");
define("IGK_ENV_URI_PATTERN_KEY", "sys://env/systemuri/patterninfo");

define("IGK_NS_PARAM_KEY", "sys://html/namespace");
define("IGK_DEFAULT_VIEW", "default");
define("IGK_DEFAULT_ARTICLE", "default");
define("IGK_DEFAULT_LANG", "fr");

define("IGK_HTML_CONTENT_TYPE", "Content-Type: text/html; charset=utf-8");

//tags for data schema
define("IGK_SCHEMA_TAGNAME", "data-schemas");
define("IGK_DATA_DEF_TAGNAME", "DataDefinition");
define("IGK_ENTRIES_TAGNAME", "Entries");
define("IGK_ROW_TAGNAME", "Row");
define("IGK_ROWS_TAGNAME", "Rows");

define("IGK_SCHEMA_FILENAME", "data.schema.xml");
define("IGK_SITEMAP_FUNC", "sitemap");
define("IGK_EVALUATE_URI_FUNC", "evaluateUri");
define("IGK_INITENV_FUNC", "InitEnvironment");

define("IGK_DATETIME_FORMAT", "Y-d-m_H:i:s");
define("IGK_COMPONENT_REG_FUNC_KEY", "sys://components/functions");
define("IGK_CURRENT_DOC_PARAM_KEY", "sys://current_document");

define("IGK_COLUMN_TAGNAME", "Column");
define("IGK_OBJ_TYPE", "igk.obj");

define("IGK_HTML_ITEMBASE_CLASS", "IGKHtmlItemBase");
define("IGK_CONFIG_MODE", "Configs");
define("IGK_CONFIG_PAGEFOLDER", "Configs");
define("IGK_REG_ACTION_METH", "(/|(/:function(/|/:params+)?)?)" );

define("IGK_REFERENCE_FOLDER", dirname(__FILE__)."/Data/References");
define("IGK_FUNC_NODE_PREFIX", "igk_html_node_");
define("IGK_FUNC_NODE_DESC_PREFIX", "igk_html_desc_");
define("IGK_FUNC_DEMO_PREFIX","igk_html_demo_");
define("IGK_FUNC_DESC_PREFIX","igk_html_desc_");
define("IGK_FUNC_CALL_IN_CONTEXT","call_incontext");
define("IGK_HOME_PAGEFOLDER","home");
define("IGK_HOME_PAGE","home");
define("IGK_FIELD_PREFIX","cl");
define("IGK_TABLE_PREFIX","tb");
define("IGK_APP_FORM_CONTENT", "Application/x-www-form-urlencoded");

define("IGK_JS_VOID", "javascript:void();");

define("IGK_DOC_TYPE", "html PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\"");


///system event
define("IGK_EVENT_DOC_BEFORE_RENDER",  "system/document/beforerender");
define("IGK_EVENT_DROP_CTRL", "sys://ctrl/ondrop");
define("IGK_FORCEVIEW_EVENT","system/notify/forceview");
define("IGK_COMP_NOT_FOUND_EVENT", "sys://component/notfound");
define("IGK_CONF_USER_CHANGE_EVENT","sys://event/config_user_changed");


define("IGK_NODE_DISPOSED_EVENT", "sys://node/disposed");

define("IGK_CONF_PAGEFOLDER_CHANGED_EVENT", "sys://event/folderchanged");

//NODE PARAM
define("IGK_NAMED_NODE_PARAM", "sys://node/namedchilds");
define("IGK_NAMED_ID_PARAM", "sys://node/namedchilds/id");


//-------------------------------------------------------------------------------------------------------------------
//ctrl config fields
//-------------------------------------------------------------------------------------------------------------------
define("IGK_CTRL_CNF_TITLE", "clTitle");
define("IGK_CTRL_CNF_APPNAME", "clAppName");
define("IGK_CTRL_CNF_BASEURIPATTERN", "clBasicUriPattern");
define("IGK_CTRL_CNF_TABLEPREFIX", "clDataTablePrefix");
define("IGK_CTRL_CNF_APPNOTACTIVE", "clAppNotActive");


//-------------------------------------------------------------------------------------------------------------------
//SESSION KEY
//-------------------------------------------------------------------------------------------------------------------
define("IGK_SESS_UNKCOLOR_KEY", "sys://session/theme/UnknownColor");
//-------------------------------------------------------------------------------------------------------------------
//ctrl config fields
//-------------------------------------------------------------------------------------------------------------------

define('IGK_HTML_EMAIL_PATTERN',"[0-9a-zA-Z]+(\.[0-9a-zA-Z]+)*@(.)+\.([a-zA-Z]{2,})");




//----------------------------------------------------------------
//----------------------------------------------------------------
define("IGK_WEB_CONTEXT", "html");
define("IGK_MAIL_CONTEXT", "mail");
define("IGK_AJX_CONTEXT", "ajx");
//----------------------------------------------------------------
// field constants
//----------------------------------------------------------------
define("IGK_FD_ID", IGK_FIELD_PREFIX."Id");
define("IGK_FD_NAME", IGK_FIELD_PREFIX."Name");
define("IGK_FD_DESC", IGK_FIELD_PREFIX."Description");
define("IGK_FD_TYPELEN", IGK_FIELD_PREFIX."TypeLength");
define("IGK_FD_TYPE", IGK_FIELD_PREFIX."Type");
define("IGK_FD_PASSWORD",IGK_FIELD_PREFIX."Pwd");
define("IGK_FD_USER_ID", IGK_FIELD_PREFIX."User_Id");
define("IGK_FD_PRODUCT_ID", IGK_FIELD_PREFIX."Product_Id");
define("IGK_FD_GROUP_ID", IGK_FIELD_PREFIX."Group_Id");
define("IGK_FD_AUTH_ID", IGK_FIELD_PREFIX."Auth_Id");

define("IGK_DEFAULT_DB_PREFIX", "tbigk");
//USER INFO TYPES
define("IGK_UINFO_TOKENID", "TOKENID");


define("IGK_CTRLBASECLASS", "IGKControllerBase");
define("IGK_CTRLNONATOMICTYPEBASECLASS", "IGKCtrlNonAtomicTypeBase");
define("IGK_CTRLWEBPAGEBASECLASS", "IGKDefaultPageCtrl");

define("IGK_CSV_SEPARATOR", "IGK_CSV_SEPARATOR");
define("IGK_CSV_SEPARATORS", ",|.|\t|;");
define("IGK_CSV_FIELD_SEPARATORS", "'|\"");
define("IGK_HTML_WHITESPACE","&nbsp;");
define("IGK_HTML_CHAR_ZERO", "&#x30;");
define("IGK_HTML_ENCTYPE","multipart/form-data");
define("IGK_MYSQL_DATAADAPTER","MYSQL");
define("IGK_CSV_DATAADAPTER","CSV");

define("IGK_CTRL_CONF_FILE", "config.xml");
define("IGK_CTRL_DBCONF_FILE", "data.xml");
define("IGK_CTRL_BASE","IGKControllerBase");


define("IGK_HTML_BINDING_EVAL_CONTEXT", "igk:evaluation_context");


//PARAMS
define("IGK_VIEW_ARGS", "sys://view/args");
define("IGK_CTRL_VIEW_CONTEXT_PARAM_KEY", "sys://view/context");


define("IGK_XML_CREATOR_PARENT_KEY", "sys://xml/create_node/parent"); //key for current parent creation. must be call before any other operation

//----------------------------------------------------------------
// FOLDERS AT ROOT 
//--------------------- -------------------------------------------
define("IGK_TEMPLATES_FOLDER", "Data/Templates");
define("IGK_LAYOUT_FOLDER", "R/Layouts");
define("IGK_STYLE_FOLDER", "Styles");
define("IGK_BACKUP_FOLDER", IGK_DATA_FOLDER."/Backup");
define("IGK_ARTICLES_FOLDER", "Articles");
define("IGK_VIEW_FOLDER", "Views");
define("IGK_PAGE_FOLDER", "Pages");
define("IGK_MODS_FOLDER", "Mods");
define("IGK_PROJECTS_FOLDER", "Projects");
define("IGK_RES_FOLDER", "R");
define("IGK_INC_FOLDER", "Inc");
define("IGK_LIB_FOLDER", "Lib");
define("IGK_SCRIPT_FOLDER","Scripts");
define("IGK_PLUGINS_FOLDER","Plugins");
define("IGK_RES_FONTS",IGK_RES_FOLDER."/Fonts");
define("IGK_DEFAULT_THEME_FOLDER", IGK_LIB_FOLDER."/igk/Default/Themes");
define("IGK_CACHE_FOLDER", "Caches");


//----------------------------------------------------------------
//FILES
//----------------------------------------------------------------
define("IGK_FILE_CTRL_CACHE", IGK_CACHE_FOLDER."/controller.cache");//class definition of included controllers
define("IGK_FILE_LIB_CACHE", IGK_CACHE_FOLDER."/lib.files.cache"); //include files on environment. used only if IGK_NO_CACHE_LIB not defined.
define("IGK_ADAPTER_CACHE",IGK_CACHE_FOLDER."/adapter.cache");//storage of present adapter
define("IGK_CACHE_DATAFILE",IGK_CACHE_FOLDER."/datafile.cache");//storage of which controlller control data table


define("IGK_FC_CALL_INC", IGK_LIB_DIR."/".IGK_INC_FOLDER."/.igk.fc.call.inc");//define a fc call

define("IGK_PIC_EXTENSIONS", ".png;.jpeg;.jpg;.bmp;.tiff;.gif;.ico;.ani");
define("IGK_PLUGIN_FILE_EXTENSIONS", ".pbal");
define("IGK_PLUGIN_ZP_FILE_EXTENSIONS", ".zpbal");
define("IGK_TEMPLATE_EXTENSIONS",".template");
define("IGK_ARTICLE_TEMPLATE_REGEX", "/\.(template|html|phtml)$/");

define("IGK_ALLOWED_EXTENSIONS", IGK_PIC_EXTENSIONS.";.avi;.mov;.flv;");
define("IGK_HTML_SPACE", "&nbsp;");
define("IGK_DEFAULT_VIEW_FILE","default.phtml");
define("IGK_DEFAULT", "default");

define("IGK_HTML_CLASS_NODE_FORMAT", "IGKHtml{0}Item");
define("IGK_HTML_NODE_REGEX", "/^IGKHtml(?<name>(.)+)Item$/i");


//controlleur names
define("IGK_SYS_CTRL", "c_sysc"); //system ctrl
define("IGK_SYS_PAGE_CTRL", "c_syspc"); //page ctrl
define("IGK_HUMAN_CTRL", "c_sys_hc");//human ctrl
define("IGK_OTHER_MENU_CTRL", "c_sys_oth_m_c");//human ctrl
define("IGK_MSBOX_CTRL", "c_msbox_c");//human ctrl
define("IGK_DATA_ADAPTER_CTRL", "c_adt_c");//human ctrl
define("IGK_SYS_API_CTRL", "c_api");

define("IGK_SYSDB_CTRL", "c_sysdbc");
define("IGK_MENU_CTRL", "c_mn");
define("IGK_FRAME_CTRL", "c_fr");
define("IGK_PIC_RES_CTRL", "c_pi");
define("IGK_CONF_CTRL", "c_cf");
define("IGK_NOTIFICATION_CTRL","c_nt");
define("IGK_CHANGE_MAN_CTRL", "c_chm");
define("IGK_CTRL_MANAGER", "c_cm");
define("IGK_SESSION_CTRL","c_sc");
define("IGK_ERROR_CTRL","c_er");
define("IGK_THEME_CTRL", "c_th");
define("IGK_FILE_MAN_CTRL", "c_fm");
define("IGK_CSVLANGUAGE_CTRL","c_lg");
define("IGK_PALETTE_CTRL", "c_pl");
define("IGK_USERVARS_CTRL", "c_uv");
define("IGK_CA_CTRL","c_ac");
define("IGK_DATA_TYPE_CTRL", "c_dtp");
define("IGK_SYSACTION_CTRL", "c_ua");
define("IGK_LANG_CTRL", "c_l");
define("IGK_LOG_CTRL", "c_lo");
define("IGK_CUSTOM_CTRL_MAN_CTRL", "c_cu");
define("IGK_LAYOUT_CTRL","c_ly");
define("IGK_COMPONENT_MANAGER_CTRL", "c_com");
define("IGK_MAIL_CTRL","c_ml");
define("IGK_REFERENCE_CTRL", "c_rf");
define("IGK_USER_CTRL", "c_u");
define("IGK_SCRIPT_CTRL","c_scpt");
define("IGK_DEBUG_CTRL","igkdebugctrl");
define("IGK_MYSQL_DB_CTRL", "IGKMYSQLDBCtrl");
define("IGK_CB_REF_CTRL", "c_cbref");
define("IGK_UCB_REF_CTRL", "c_ucbref");
define("IGK_SHARED_CONTENT_CTRL", "c_shc");
define("IGK_DOC_CTRL", "c_docs");
define("IGK_BDCONFIGS_CTRL", "c_configs");
//AJX CONSTANT
define("IGK_AJX_METHOD_SUFFIX","_ajx");
//ERROR MESSAGE


define("IGK_CONFIRM_TITLE","title.confirm");
define("IGK_PAGE_TITLE","title.default.webpage");
//QUESTION MESSAGE
define("IGK_MSG_DELETEALLFONT_QUESTION", "Msg.DeleteAllFontQuestion");
define("IGK_MSG_DELETEFILE_QUESTION","Msg.DELETEFILEQUESTION");
define("IGK_MSG_DELETEDIR_QUESTION","Msg.DELELETEDIRQUESTION");
define("IGK_MSG_DELETECTRL_QUESTION", "Msg.DELETECTRLQUESTION");
define("IGK_MSG_ALLPICS_QUESTION", "Msg.DELETEALLPICSQUESTION");
define("IGK_MSG_DELETEMENU_QUESTION", "Msg.DELETEALLMENUQUESTION");
define("IGK_DELETEALLTABLE_QUESTION", "Msg.DELETEALLTABLEQUESTION");
define("IGK_MSG_DELETEALLDATABASEBACKUP_QUESTION", "Msg.DELETEALLDATABASEBACKUPQUESTION");
define("IGK_MSG_DELETESINGLETABLE_QUESTION","Msg.DELETESINGLETABLEQUESTION_1");
define("IGK_MSG_DELETEABACKUPFILE_QUESTION", "Msg.DELETEABACKUPFILEQUESTION");
define("IGK_MSG_RESTOREBACKUPFILE_QUESTION", "Msg.RESTOREBACKUPFILEQUESTION");
define("IGK_MSG_DROPALL_QUESTION", "Msg.DROPALLQUESTION");
define("IGK_CONF_PAGE_TITLE", "title.CONFIGPAGE");

define("ALL_LANG","all-lang");

//primary table declaration
//----------------------------------------------------------------
//DATA TABLE
//----------------------------------------------------------------
define("IGK_TB_GROUPS","tbigk_groups");
define("IGK_TB_USERS","tbigk_users");
define("IGK_TB_USER_INFOS","tbigk_user_infos");
define("IGK_TB_USER_INFO_TYPES","tbigk_user_info_types");
define("IGK_TB_WHO_USES", "tbigk_who_uses");
define("IGK_TB_AUTHORISATIONS", "tbigk_authorizations");
define("IGK_TB_SUBDOMAIN","tbigk_subdomain");
define("IGK_TB_USERGROUPS", "tbigk_usergroups");
define("IGK_TB_GROUPAUTHS", "tbigk_groupauthorizations");
define("IGK_TB_DATATYPES", "tbigk_data_types");
define("IGK_TB_HUMAN","tbigk_humans");
define("IGK_TB_CONFIGS","tbigk_configurations");
define("IGK_TB_COMMUNITY","tbigk_community");
define("IGK_TB_SYSTEMURI","tbigk_systemuri");
define("IGK_TB_COOKIESTORE","tbigk_cookie_storages");
define("IGK_START_COMMENT", "/*");
define("IGK_END_COMMENT", "*/");

//-------------------------------------------------------------------------------
//regex definition
//-------------------------------------------------------------------------------
define("IGK_IPV4_REGEX", "/^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}(:[0-9]+)?$/i");

define("IGK_CSS_TREAT_REGEX", '/\[\s*(?P<name>[\w\-]+)\s*:\s*(?P<value>[a-zA-Z0-9_,\/\\\.\- \(\)%:\#\!]+)\s*\](\s*(,|;)*)*/i');
define("IGK_CSS_TREAT_REGEX_2",'/\{\s*(?P<name>[\w:\-_,\!\s]+)\s*\}\s*(;)*/i');
define("IGK_CSS_CHILD_EXPRESSION_REGEX", "/^\s*\(:(?P<name>([a-z0-9_\-\.])+)\)\s*$/i");

define("IGK_SUBDOMAIN_URI_NAME_REGEX","/^(?P<name>[\.\-_0-9a-z]+)\.([^\.]+)\.([^\.]+)$/i");
define("IGK_ALL_REGEX", "/(.)*/i");
define("IGK_VIEW_FILE_EXT_REGEX", "phtml|bvhtml");
define("IGK_VIEW_FILE_END_REGEX","/(.)+(\.(".IGK_VIEW_FILE_EXT_REGEX."))?$/i");

define("IGK_APP_LOGO", "/R/Img/app_logo.png");




//$igk_error_codes = array(); 

if (!function_exists("igk_define_error"))
{	
	// function igk_define_error($msg, $code, $msg_key=null){
		// global $igk_error_codes;
		// $igk_error_codes[$msg] = array("Key"=>$msg,"Code"=>$code, "Msg"=> ($msg_key==null)?str_replace("_",".", $msg):$msg_key );
		// define($msg, $msg);
	// }
	
	function igk_define_error($msg, $code, $msg_key=null){
		igk_error_def_error($msg, $code, $msg_key);
		
		
		// global $igk_error_codes;
		// $igk_error_codes[$msg] = array("Key"=>$msg,"Code"=>$code, "Msg"=> ($msg_key==null)?str_replace("_",".", $msg):$msg_key );
		// define($msg, $msg);
	}
}
/*
class IGKErrorManager
{
	static $igk_error_codes = array(); 
	
	public static function DefineError($msg, $code, $msg_key=null){
		self::$igk_error_codes[$msg] = array("Key"=>$msg,"Code"=>$code, "Msg"=> ($msg_key==null)?str_replace("_",".", $msg):$msg_key );
		define($msg, $msg);
	}
	static function  error($code){
	//global $igk_error_codes;
	//return $igk_error_codes[$code];
		return self::$igk_error_codes[$code];
	}
	static function  geterror_code($code){
		//global $igk_error_codes;
		return self::$igk_error_codes[$code]["Code"];
	}
	static function  geterror_key($code){
		//global $igk_error_codes;
		return self::$igk_error_codes[$code]["Msg"];
	}
}
*/
// $igk_error_codes = array(); 
function igk_error($code){
	return igk_getv(igk_get_env("sys://error_codes"),$code);
	//global $igk_error_codes;
	//return $igk_error_codes[$code];
	//return $igk_error_codes[$code]; //IGKErrorManager::error($code);
}
function igk_geterror_code($code){
	return igk_error($code)["Code"];
	// global $igk_error_codes;
	// return $igk_error_codes[$code]["Code"]; //IGKErrorManager::geterror_code($code);
}
function igk_get_error_key($code){
	return igk_error($code)["Msg"];
	// global $igk_error_codes;
	// // return $igk_error_codes[$code]["Msg"];
	// return $igk_error_codes[$code]["Msg"];//IGKErrorManager::geterror_key($code);
}
function igk_error_def_error($msg, $code, $msg_key){
	//IGKErrorManager::DefineError($msg, $code, $msg_key);
	$igk_error_codes = igk_get_env("sys://error_codes", function(){return array();});
	//global $igk_error_codes;
	$igk_error_codes[$msg] = array("Key"=>$msg,"Code"=>$code, "Msg"=> ($msg_key==null)?str_replace("_",".", $msg):$msg_key );
	define($msg, $msg);
	igk_set_env("sys://error_codes", $igk_error_codes);
}


igk_define_error("IGK_ERR_USERERROR", 10400);
igk_define_error("IGK_ERR_NOUSERFOUND",igk_geterror_code(IGK_ERR_USERERROR)+1, "ERR.NoUserFOUND");
igk_define_error("IGK_ERR_PAGENULLOREMPTY", igk_geterror_code(IGK_ERR_USERERROR)+0x0002, "ERR.PAGENULLOREMPTY");
igk_define_error("IGK_ERR_LOGORPWDNOTVALID",igk_geterror_code(IGK_ERR_USERERROR)+0x0003, "ERR.LoginOrPWDNotValid");
igk_define_error("IGK_ERR_NOT_FROM_LOCAL", igk_geterror_code(IGK_ERR_USERERROR)+0x0100, "ERR.REQUESTFROMNONLOCAL");
igk_define_error("IGK_ERR_REQUEST_NOT_FROM_BALAFON_SERVER", igk_geterror_code(IGK_ERR_USERERROR)+0x0101, "ERR.REQUESTFROMABALAFONSERVER");


igk_define_error("IGK_ERR_PERMISSION", 2100, "e.youdonthavecorrectperm");
igk_define_error("IGK_ERR_FUNCNOTAVAILABLE", igk_geterror_code(IGK_ERR_PERMISSION)+1, "e.funcnotavailable");

igk_define_error("ERR_FILE_NOT_SUPPORTED",10080);
igk_define_error("ERR_SCRIPT_ERROR", 110100);


define("IGK_ERR_CTRL_", 0x10000);
define("IGK_ERR_NO_PAGEVIEW", 0x1000A);

//-----------------------------------------------------------------------------
//notification message
//-----------------------------------------------------------------------------
define("IGK_NOTIFICATION_DB_CHANGED", "system/db/changed");
define("IGK_NOTIFICATION_DB_TABLECREATED", "system/db/tablecreated");
define("IGK_NOTIFICATION_DB_TABLEDROPPED", "system/db/tabledropped");
define("IGK_NOTIFICATION_APP_DOWNLOADED", "system/apps/downloaded");
define("IGK_NOTIFICATION_USER_CHANGED", "system/userchanged");
define("IGK_NOTIFICATION_INITTABLE","sys://db/initable");


// $notification = array();
// $notification[IGK_NOTIFICATION_DB_CHANGED] = "inform db global change";
// $notification[IGK_NOTIFICATION_DB_TABLECREATED] = "new table created";

igk_set_env("sys://notification/msgs", array(
"en"=>array(
IGK_NOTIFICATION_DB_CHANGED => "inform db global change",
IGK_NOTIFICATION_DB_TABLECREATED => "new table created"
)
));

igk_set_error_msg(array(
"en"=>array(
	IGK_ERR_NO_PAGEVIEW=>"No pageview defined for {1} you class probably doesn't call the base construct"
)
));




define("IGK_DOC_ERROR_ID", "sys://document/ids/error"); //the error id for errordocument
define("IGK_DOC_CONF_ID", "sys://document/ids/config"); //the config docmuent id - not used

define("IGK_ENV_CURRENT_RENDERING_DOC", "sys://env/renderDocument"); //store the current rendering document before rendering


?>