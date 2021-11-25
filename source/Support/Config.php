<?php
/**
 * DATABASE
 */
const CONF_DB_HOST = "localhost";
const CONF_DB_USER = "root";
const CONF_DB_PASS = "root";
const CONF_DB_NAME = "mvcoo";

/**
 * PROJECT URLs
 */
const CONF_URL_BASE = "http://localhost/mvc_php_oo";
const CONF_URL_ADMIN = "/admin";

/**
 * SITE
 */
const CONF_SITE_NAME = "FredDev";
const CONF_SITE_LANG = "pt-BR";
const CONF_SITE_DOMAIN = "wwww.freddev.com";

/**
 * SOCIAL
 */
const CONF_SOCIAL_TWITTER_CREATOR = "@fred";
const CONF_SOCIAL_TWITTER_PUBLISHER = "@fred";
const CONF_SOCIAL_FACEBOOK_APP = "586693779218787";
const CONF_SOCIAL_FACEBOOK_PAGE = "FredDev";
const CONF_SOCIAL_FACEBOOK_AUTHOR = "frederico.pereira.9";

/**
 * DATES
 */
const CONF_DATE_BR = "d/m/Y H:i:s";
const CONF_DATE_APP = "Y-m-d H:i:s";

/**
 * PASSWORD
 */
const CONF_PASSWD_MIN_LEN = 8;
const CONF_PASSWD_MAX_LEN = 40;
const CONF_PASSWD_ALGO = PASSWORD_DEFAULT;
const CONF_PASSWD_OPTION = ['cost' => 10];

/**
 * MESSAGE
 */
const CONF_MESSAGE_CLASS = "trigger";
const CONF_MESSAGE_INFO = "info";
const CONF_MESSAGE_SUCCESS = "success";
const CONF_MESSAGE_WARNING = "warning";
const CONF_MESSAGE_ERROR = "error";

/**
 * VIEW
 */
const CONF_VIEW_PATH = __DIR__ . "/../../shared/views";
const CONF_VIEW_EXT = "php";

/**
 * UPLOAD
 */
const CONF_UPLOAD_DIR = "../storage";
const CONF_UPLOAD_IMAGE_DIR = "images";
const CONF_UPLOAD_FILE_DIR = "files";
const CONF_UPLOAD_MEDIA_DIR = "medias";

/**
 * IMAGES
 */
const CONF_IMAGE_CACHE = CONF_UPLOAD_DIR . "/" . CONF_UPLOAD_IMAGE_DIR . "/cache";
const CONF_IMAGE_SIZE = 2000;
const CONF_IMAGE_QUALITY = ["jpg" => 75, "png" => 5];

/**
 * MAIL
 */
const CONF_MAIL_HOST = "smtp.gmail.com";
const CONF_MAIL_PORT = "587";
const CONF_MAIL_USER = "fredericosantana11@gmail.com";
const CONF_MAIL_PASS = "engels967947";
const CONF_MAIL_SENDER = [
  "name" => "Frederico Santana",
  "address" => "fredericosantana11@gmail.com"
];
const CONF_MAIL_OPTIONS_LANG = "br";
const CONF_MAIL_OPTIONS_HTML = "true";
const CONF_MAIL_OPTIONS_AUTH = "true";
const CONF_MAIL_OPTIONS_SECURE = "tls";
const CONF_MAIL_OPTIONS_CHARSET = "utf-8";