<?php
/**
 * DATABASE
 */
const CONF_DB_HOST = "localhost";
const CONF_DB_USER = "root";
const CONF_DB_PASS = "";
const CONF_DB_NAME = "mvcoo";

/**
 * PROJECT URLs
 */
const CONF_URL_BASE = "https://www.localhost/cafecontrol";
const CONF_URL_ADMIN = "/admin";

/**
 * SITE
 */
const CONF_SITE_NAME = "Café Control";
const CONF_SITE_TITLE = "Gerencie suas contas com o melhor café.";
const CONF_SITE_DESC = "O CafeControl é um gerenciador de contas simples, poderoso e gratuito. O prazer de tomar um café e ter o controle total de suas contas.";
const CONF_SITE_LANG = "pt-BR";
const CONF_SITE_DOMAIN = "wwww.freddev.com";
const CONF_SITE_ADDR_STREET = "Rua Barão do Rio Branco";
const CONF_SITE_ADDR_NUMBER = "800";
const CONF_SITE_ADDR_COMPLEMENT = "Casa 06";
const CONF_SITE_ADDR_CITY = "Goiânia";
const CONF_SITE_ADDR_STATE = "Goiás";
const CONF_SITE_ADDR_ZIPCODE = "74340-040";

/**
 * SOCIAL
 */
const CONF_SOCIAL_TWITTER_CREATOR = "@fred";
const CONF_SOCIAL_TWITTER_PUBLISHER = "@fred";
const CONF_SOCIAL_FACEBOOK_APP = "586693779218787";
const CONF_SOCIAL_FACEBOOK_PAGE = "frederico.pereira.9";
const CONF_SOCIAL_FACEBOOK_AUTHOR = "frederico.pereira.9";
const CONF_SOCIAL_INSTAGRAM_PAGE = "frediara";
const CONF_SOCIAL_YOUTUBE_PAGE = "frederico.pereira.9";

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
 * VIEW
 */
const CONF_VIEW_PATH = __DIR__ . "/../../themes";
const CONF_VIEW_EXT = "php";
const CONF_VIEW_THEME = "cafeweb";
const CONF_VIEW_APP = "cafeapp";

/**
 * UPLOAD
 */
const CONF_UPLOAD_DIR = "storage";
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
const CONF_MAIL_PASS = "Engels%967947!";
const CONF_MAIL_SENDER = [
  "name" => "Frederico Santana",
  "address" => "fredericosantana11@gmail.com"
];
const CONF_MAIL_SUPPORT = "fredericosantana11@gmail.com";
const CONF_MAIL_OPTION_LANG = "br";
const CONF_MAIL_OPTION_HTML = "true";
const CONF_MAIL_OPTION_AUTH = "true";
const CONF_MAIL_OPTION_SECURE = "tls";
const CONF_MAIL_OPTION_CHARSET = "utf-8";