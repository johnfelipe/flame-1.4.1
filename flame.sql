-- phpMyAdmin SQL Dump
-- version 5.0.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 18, 2021 at 06:47 PM
-- Server version: 10.4.14-MariaDB
-- PHP Version: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `flame`
--

-- --------------------------------------------------------

--
-- Table structure for table `fl_admininvitations`
--

CREATE TABLE `fl_admininvitations` (
  `id` int(11) NOT NULL,
  `code` varchar(300) NOT NULL DEFAULT '0',
  `posted` varchar(50) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `fl_ads`
--

CREATE TABLE `fl_ads` (
  `id` int(11) NOT NULL,
  `type` varchar(32) NOT NULL DEFAULT '',
  `code` text DEFAULT NULL,
  `active` enum('0','1') NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `fl_ads`
--

INSERT INTO `fl_ads` (`id`, `type`, `code`, `active`) VALUES
(1, 'header', NULL, '0'),
(2, 'footer', NULL, '0'),
(3, 'sidebar', NULL, '0'),
(4, 'home_latest_news', NULL, '0'),
(5, 'home_latest_lists', NULL, '0'),
(6, 'home_latest_videos', NULL, '0'),
(7, 'home_latest_music', NULL, '0'),
(8, 'between', NULL, '0');

-- --------------------------------------------------------

--
-- Table structure for table `fl_announcement`
--

CREATE TABLE `fl_announcement` (
  `id` int(11) NOT NULL,
  `text` text DEFAULT NULL,
  `time` int(32) NOT NULL DEFAULT 0,
  `active` enum('0','1') NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `fl_announcement_views`
--

CREATE TABLE `fl_announcement_views` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT 0,
  `announcement_id` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `fl_bank_receipts`
--

CREATE TABLE `fl_bank_receipts` (
  `id` int(11) UNSIGNED NOT NULL,
  `user_id` int(11) UNSIGNED NOT NULL DEFAULT 0,
  `fund_id` int(11) NOT NULL DEFAULT 0,
  `description` tinytext NOT NULL,
  `price` varchar(50) NOT NULL DEFAULT '0',
  `mode` varchar(50) NOT NULL DEFAULT '',
  `approved` int(11) UNSIGNED NOT NULL DEFAULT 0,
  `receipt_file` varchar(250) NOT NULL DEFAULT '',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `approved_at` int(11) UNSIGNED NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `fl_banned_ip`
--

CREATE TABLE `fl_banned_ip` (
  `id` int(11) NOT NULL,
  `ip_address` varchar(32) NOT NULL DEFAULT '',
  `time` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `fl_br_news`
--

CREATE TABLE `fl_br_news` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT 0,
  `expire` varchar(50) NOT NULL DEFAULT '0',
  `url` varchar(3000) NOT NULL DEFAULT '',
  `text` varchar(1000) NOT NULL DEFAULT '',
  `active` enum('0','1') NOT NULL DEFAULT '0',
  `time` varchar(50) NOT NULL DEFAULT '0',
  `posted` varchar(50) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `fl_comments`
--

CREATE TABLE `fl_comments` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `news_id` int(11) NOT NULL DEFAULT 0,
  `text` text DEFAULT NULL,
  `likes` varchar(10000) NOT NULL DEFAULT 'a:0:{}',
  `dislikes` varchar(10000) NOT NULL DEFAULT 'a:0:{}',
  `time` varchar(30) NOT NULL DEFAULT '0',
  `page` varchar(10) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `fl_comm_replies`
--

CREATE TABLE `fl_comm_replies` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT 0,
  `news_id` int(11) NOT NULL DEFAULT 0,
  `comment` int(11) NOT NULL DEFAULT 0,
  `text` text DEFAULT NULL,
  `likes` varchar(10000) NOT NULL DEFAULT 'a:0:{}',
  `dislikes` varchar(10000) NOT NULL DEFAULT 'a:0:{}',
  `time` varchar(30) NOT NULL DEFAULT '0',
  `page` varchar(10) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `fl_config`
--

CREATE TABLE `fl_config` (
  `id` int(11) NOT NULL,
  `name` varchar(32) NOT NULL DEFAULT '',
  `value` text DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `fl_config`
--

INSERT INTO `fl_config` (`id`, `name`, `value`) VALUES
(1, 'name', 'Flame'),
(2, 'theme', 'default'),
(3, 'title', 'Flame'),
(4, 'validation', '2'),
(5, 'registration', '1'),
(6, 'language', 'english'),
(7, 'smtp_or_mail', 'mail'),
(8, 'smtp_host', ''),
(9, 'smtp_username', ''),
(10, 'smtp_password', ''),
(11, 'smtp_encryption', 'tls'),
(12, 'smtp_port', ''),
(13, 'email', 'deendoughouz@gmail.com'),
(14, 'reCaptcha', '2'),
(15, 'google_app_ID', ''),
(16, 'google_app_key', ''),
(17, 'facebook_app_ID', 'asdasd'),
(18, 'facebook_app_key', 'asd'),
(19, 'twitter_app_ID', ''),
(20, 'twitter_app_key', ''),
(21, 'maintenance', '1'),
(22, 'delete_account', '1'),
(23, 'keywords', 'flame,viral,media,social media,videos,content'),
(24, 'description', 'FLAME is a PHP Viral Media Script, FLAME is the best way to start your own social media and viral website ! FLAME is fast, secured, and it will be regularly updated.'),
(26, 'censored', ''),
(27, 'reCaptcha_key', ''),
(28, 'analytics', ''),
(29, 'upload', '12000000'),
(33, 'facebook', '1'),
(34, 'twitter', '1'),
(35, 'google', '1'),
(36, 'fb_page', 'https://www.facebook.com/facebook/'),
(37, 'wowonder_app_key', ''),
(38, 'wowonder_app_ID', ''),
(39, 'wowonder', '1'),
(40, 'wownder_domain_uri', ''),
(41, 'wownder_site_name', 'asdasd'),
(42, 'vkontakte_app_key', ''),
(43, 'vkontakte', '1'),
(44, 'vkontakte_app_ID', ''),
(45, 'vkontakte_app_key', ''),
(46, 'rss_feed', ''),
(47, 'rss_feed_limit', '10'),
(48, 'theme', 'default'),
(49, 'logo_extension', 'png'),
(50, 'icon_extension', 'png'),
(51, 'logo', 'themes/default/img/logo.png'),
(52, 'favicon', 'themes/default/img/icon.png'),
(53, 'news', '1'),
(54, 'lists', '1'),
(55, 'polls', '1'),
(56, 'music', '1'),
(57, 'quizzes', '1'),
(58, 'videos', '1'),
(59, 'last_backup', '00-00-0000'),
(60, 'can_post', '1'),
(61, 'header_ccx', '/* Add here your JavaScript Code. Note. the code entered here will be added in <head> tag Example: var x, y, z; x = 5; y = 6; z = x + y; */'),
(62, 'footer_ccx', ' /* The code entered here will be added in <footer> tag */'),
(63, 'styles_ccx', '/* Add here your custom css styles Example: p { text-align: center; color: red; } */ '),
(64, 'amazone_s3', '0'),
(65, 'bucket_name', ''),
(66, 'amazone_s3_key', ''),
(67, 'amazone_s3_s_key', ''),
(68, 'region', ''),
(69, 'apps_api_id', '1ffa3c7d5195d13dc00e88d9ed68336d'),
(70, 'comment_system', 'default'),
(71, 'apps_api_key', '500e47f2fdc2b9ee3260e4d49e3ebe94'),
(72, 'google_code', ''),
(73, 'review_posts', '0'),
(74, 'pro_pkg_price', '23'),
(75, 'go_pro', '1'),
(76, 'user_max_posts', '10'),
(77, 'paypal_mode', 'sandbox'),
(78, 'paypal_id', ''),
(79, 'paypal_secret', ''),
(80, 'ad_c_price', '0.4'),
(81, 'usr_ads', '1'),
(82, 'show_subscribe_box', '0'),
(83, 'subscribe_box_username', 'phpflame'),
(84, 'trivia', '1'),
(85, 'switch_account', '1'),
(86, 'credit_card', 'no'),
(87, 'stripe_secret', ''),
(88, 'stripe_id', ''),
(89, 'checkout_payment', 'no'),
(90, 'checkout_mode', 'sandbox'),
(91, 'checkout_seller_id', ''),
(92, 'checkout_publishable_key', ''),
(93, 'checkout_private_key', ''),
(94, 'paystack_payment', 'no'),
(95, 'paystack_secret_key', ''),
(96, 'cashfree_payment', 'no'),
(97, 'cashfree_mode', 'sandBox'),
(98, 'cashfree_client_key', ''),
(99, 'cashfree_secret_key', ''),
(100, 'razorpay_payment', 'no'),
(101, 'razorpay_key_id', ''),
(102, 'razorpay_key_secret', ''),
(103, 'paysera_payment', 'no'),
(104, 'paysera_mode', '1'),
(105, 'paysera_project_id', ''),
(106, 'paysera_sign_password', ''),
(107, 'iyzipay_payment', 'no'),
(108, 'iyzipay_mode', '1'),
(109, 'iyzipay_key', ''),
(110, 'iyzipay_secret_key', ''),
(111, 'iyzipay_buyer_id', ''),
(112, 'iyzipay_buyer_name', ''),
(113, 'iyzipay_buyer_surname', ''),
(114, 'iyzipay_buyer_gsm_number', ''),
(115, 'iyzipay_buyer_email', ''),
(116, 'iyzipay_identity_number', ''),
(117, 'iyzipay_address', ''),
(118, 'iyzipay_city', ''),
(119, 'iyzipay_country', ''),
(120, 'iyzipay_zip', ''),
(121, 'bank_payment', 'no'),
(122, 'bank_transfer_note', 'In order to confirm the bank transfer, you will need to upload a receipt or take a screenshot of your transfer within 1 day from your payment date. If a bank transfer is made but no receipt is uploaded within this period, your order will be cancelled. We will verify and confirm your receipt within 3 working days from the date you upload it.'),
(123, 'live_video', '0'),
(124, 'live_video_save', '0'),
(125, 'agora_live_video', '0'),
(126, 'agora_app_id', ''),
(127, 'agora_customer_id', ''),
(128, 'agora_customer_certificate', ''),
(129, 'amazone_s3_2', '0'),
(130, 'bucket_name_2', ''),
(131, 'amazone_s3_key_2', ''),
(132, 'amazone_s3_s_key_2', ''),
(133, 'region_2', 'eu-west-1'),
(134, 'flip', '1'),
(135, 'paypal', 'no'),
(136, 'script_version', '1.4.1'),
(137, 'bank_description', '<div class=\"bank_info\"><div class=\"dt_settings_header bg_gradient\"><div class=\"dt_settings_circle-1\"></div><div class=\"dt_settings_circle-2\"></div><div class=\"bank_info_innr\"><svg xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 24 24\"><path fill=\"currentColor\" d=\"M11.5,1L2,6V8H21V6M16,10V17H19V10M2,22H21V19H2M10,10V17H13V10M4,10V17H7V10H4Z\"></path></svg><h4 class=\"bank_name\">Garanti Bank</h4><div class=\"row\"><div class=\"col col-md-12\"><div class=\"bank_account\"><p>4796824372433055</p><span class=\"help-block\">Account number / IBAN</span></div></div><div class=\"col col-md-12\"><div class=\"bank_account_holder\"><p>Antoian Kordiyal</p><span class=\"help-block\">Account name</span></div></div><div class=\"col col-md-6\"><div class=\"bank_account_code\"><p>TGBATRISXXX</p><span class=\"help-block\">Routing code</span></div></div><div class=\"col col-md-6\"><div class=\"bank_account_country\"><p>United States</p><span class=\"help-block\">Country</span></div></div></div></div></div></div>'),
(138, 'english', '1'),
(139, 'arabic', '1'),
(140, 'french', '1'),
(141, 'germen', '1'),
(142, 'russian', '1'),
(143, 'turkish', '1');

-- --------------------------------------------------------

--
-- Table structure for table `fl_custompages`
--

CREATE TABLE `fl_custompages` (
  `id` int(11) NOT NULL,
  `page_name` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `page_title` varchar(200) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `page_content` text COLLATE utf8_unicode_ci DEFAULT NULL,
  `page_type` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fl_entries`
--

CREATE TABLE `fl_entries` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT 0,
  `post_id` int(11) NOT NULL DEFAULT 0,
  `index_id` int(11) NOT NULL DEFAULT 0,
  `entry_type` varchar(32) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `text` text CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `image` varchar(120) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `facebook_post` varchar(120) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `tweet` text CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `tweet_url` varchar(200) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `soundcloud_id` varchar(120) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `instagram` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `instagram_url` varchar(200) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `title` text CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `video` varchar(120) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `video_type` varchar(32) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `video_url` varchar(200) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `entry_page` varchar(32) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'news',
  `price` int(11) UNSIGNED NOT NULL DEFAULT 0,
  `time` int(11) NOT NULL DEFAULT 0
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fl_fav`
--

CREATE TABLE `fl_fav` (
  `id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL DEFAULT 0,
  `user_id` int(11) NOT NULL DEFAULT 0,
  `page` varchar(20) NOT NULL DEFAULT '',
  `time` int(20) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `fl_langs`
--

CREATE TABLE `fl_langs` (
  `id` int(11) NOT NULL,
  `lang_key` varchar(160) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `english` text DEFAULT NULL,
  `arabic` text CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `french` text CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `germen` text CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `russian` text CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `turkish` text CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `fl_langs`
--

INSERT INTO `fl_langs` (`id`, `lang_key`, `english`, `arabic`, `french`, `germen`, `russian`, `turkish`) VALUES
(1, 'male', 'Male', 'الذكر', 'Mâle', 'Männlich', 'мужчина', 'Erkek'),
(2, 'female', 'Female', 'إناثا', 'Femelle', 'Weiblich', 'женский', 'Kadın'),
(3, 'year', 'year', 'عام', 'an', 'Jahr', 'год', 'yıl'),
(4, 'month', 'month', 'شهر', 'mois', 'Monat', 'месяц', 'ay'),
(5, 'day', 'day', 'يوم', 'journée', 'Tag', 'день', 'gün'),
(6, 'hour', 'hour', 'ساعة', 'heure', 'Stunde', 'час', 'saat'),
(7, 'minute', 'minute', 'اللحظة', 'minute', 'Minute', 'минут', 'dakika'),
(8, 'second', 'second', 'ثانيا', 'seconde', 'zweite', 'второй', 'ikinci'),
(9, 'years', 'years', 'سنوات', 'années', 'Jahre', 'лет', 'yıl'),
(10, 'months', 'months', 'الشهور', 'mois', 'Monate', 'месяцы', 'ay'),
(11, 'days', 'days', 'أيام', 'journées', 'Tage', 'дней', 'günler'),
(12, 'hours', 'hours', 'ساعات', 'heures', 'Std.', 'часов', 'saatler'),
(13, 'minutes', 'minutes', 'الدقائق', 'minutes', 'Protokoll', 'минут', 'dakika'),
(14, 'seconds', 'seconds', 'ثواني', 'Secondes', 'Sekunden', 'секунд', 'saniye'),
(15, 'time_ago', 'ago', 'منذ', 'depuis', 'vor', 'тому назад', 'önce'),
(16, '404_notfound', '404, Not found !', '404 غير موجود !', '404, Pas trouvé!', '404 Nicht gefunden !', '404 Не Найдено !', '404 Bulunamadı !'),
(17, '404_desc', 'Sorry, the link your followed is not found or broken.', 'عذرا، لم يتم العثور على الرابط الذي اتبعته أو تم تعطيله.', 'Désolé, le lien que vous avez suivi n&#039;est pas trouvé ou brisé.', 'Entschuldigung, der Link wurde nicht gefunden oder gebrochen.', 'Извините, ссылка, за которой вы следовали, не найдена или не сломана.', 'Maalesef, takip ettiğiniz bağlantı bulunamadı veya bozuk.'),
(18, 'congratulations', 'Congratulations!', 'تهانينا!', 'Toutes nos félicitations!', 'Glückwünsche!', 'Поздравления!', 'Tebrik ederiz!'),
(19, 'your_account_is_activated', 'Your account have been successfully activated, &lt;a href=&quot;http://localhost/flame/&quot;&gt;Let&#039;s start !&lt;/a&gt;', 'تم تنشيط حسابك بنجاح، &lt;a href=&#039;&#039;&gt; لنبدأ! &lt;/a&gt;', 'Votre compte a été activé avec succès, &lt;a href=&#039;&#039;&gt; Commençons! &lt;/a&gt;', 'Ihr Konto wurde erfolgreich aktiviert, &lt;a href=&#039;&#039;&gt; Let&quot;s start! &lt;/a&gt;', 'Ваша учетная запись успешно активирована, &lt;a href=&#039;&#039;&gt; Начнем! &lt;/a&gt;', 'Hesabınız başarıyla etkinleştirildi, &lt;a href=&#039;&#039;&gt; Başlayalım! &lt;/a&gt;'),
(20, 'oops', 'Ooops!', 'خطأ!', 'Ooops!', 'Ooops!', 'По электронной почте Ой!', 'Posta ile gönder'),
(21, 'your_account_is_error', 'Error found while activating your account, Please try again later, Back to &lt;a href=&quot;http://localhost/flame/&quot;&gt;Home&lt;/a&gt;', 'حدث خطأ أثناء تنشيط حسابك، يرجى إعادة المحاولة لاحقا، رجوع إلى &lt;a href=&#039;&#039;&gt; الصفحة الرئيسية &lt;/a&gt;', 'Une erreur s&#039;est produite lors de l&#039;activation de votre compte. Veuillez réessayer ultérieurement. Retour à &lt;a href=&#039;&#039;&gt; Accueil &lt;/a&gt;', 'Fehler bei der Aktivierung Ihres Kontos gefunden, bitte versuchen Sie es später erneut, Zurück zu &lt;a href=&#039;&#039;&gt; Home &lt;/a&gt;', 'Ошибка при активации вашей учетной записи. Повторите попытку позже, Вернуться к &lt;a href=&#039;&#039;&gt; Главная &lt;/a&gt;', 'Hesabınızı etkinleştirirken bir hata bulundu, lütfen daha sonra tekrar deneyin, &lt;a href=&#039;&#039;&gt; Ana Sayfa &lt;/a&gt; ye dönün'),
(22, 'advertisement', 'Advertisement', 'الإعلانات', 'Publicité', 'Werbung', 'Реклама', 'reklâm'),
(23, 'dashbaord', 'Dashbaord', 'Dashbaord', 'Dashbaord', 'Dashbaord', 'Dashbaord', 'tablonuzda'),
(24, 'total_users', 'Total Users', 'إجمالي المستخدمين', 'Nombre total d&#039;utilisateurs', 'Gesamtbenutzer', 'Всего пользователей', 'Toplam Kullanıcı'),
(25, 'news', 'News', 'أخبار', 'Nouvelles', 'Nachrichten', 'Новости', 'haber'),
(26, 'lists', 'Lists', 'قوائم', 'Listes', 'Listen', 'Списки', 'Listeler'),
(27, 'videos', 'Videos', 'أشرطة فيديو', 'Vidéos', 'Videos', 'Видео', 'Videolar'),
(28, 'music', 'Music', 'موسيقى', 'La musique', 'Musik', 'Музыка', 'Müzik'),
(29, 'polls', 'Polls', 'استطلاعات الرأي', 'Les sondages', 'Umfragen', 'Опросы', 'Anketler'),
(30, 'drafts', 'Drafts', 'لعبة الداما', 'Brouillons', 'Entwürfe', 'Черновики', 'Taslaklar'),
(31, 'online_users', 'Online Users', 'مستخدمين على الهواء', 'Utilisateurs en ligne', 'Online Benutzer', 'Пользователи онлайн', 'Çevrimiçi Kullanıcılar'),
(32, 'users', 'Users', 'المستخدمين', 'Utilisateurs', 'Benutzer', 'пользователей', 'Kullanıcılar'),
(33, 'email_settings', 'E-mail Settings', 'إعدادات البريد الإلكتروني', 'Paramètres de messagerie', 'Email Einstellungen', 'Настройки электронной почты', 'E-posta Ayarları'),
(34, 'general_settings', 'General Settings', 'الاعدادات العامة', 'réglages généraux', 'Allgemeine Einstellungen', 'общие настройки', 'Genel Ayarlar'),
(35, 'maintenance_mode', 'Maintenance mode', 'نمط الصيانة', 'Mode de Maintenance', 'Wartungsmodus', 'Режим обслуживания', 'Bakım Modu'),
(36, 'user_registration', 'User registration', 'تسجيل المستخدم', 'Enregistrement de l&#039;utilisateur', 'Benutzer Registration', 'Регистрация пользователя', 'Kullanıcı kaydı'),
(37, 'admin_main_mode_desc', 'Turn the whole site in Maintenance mode. &lt;Br&gt;You can get the site back by visiting /admincp', 'قم بتشغيل الموقع بأكمله في وضع الصيانة. &lt;br&gt; يمكنك الحصول على الموقع مرة أخرى عن طريق زيارة / أدمينكب', 'Tournez tout le site en mode Maintenance. &lt;br&gt; Vous pouvez récupérer le site en visitant / admincp', 'Drehen Sie den gesamten Standort im Wartungsmodus. &lt;Br&gt; Sie können die Seite zurück besuchen, indem Sie / admincp besuchen', 'Поверните весь сайт в режиме обслуживания. &lt;Br&gt; Вы можете получить сайт обратно, посетив / admincp', 'Sitenin tamamını Bakım modunda açın. / Admincp adresini ziyaret ederek siteyi geri alabilirsiniz.'),
(38, 'enabled', 'Enabled', 'تمكين', 'Activée', 'Aktiviert', 'Включено', 'Etkin'),
(39, 'disabled', 'Disabled', 'معاق', 'désactivé', 'Behindert', 'Отключено', 'engelli'),
(40, 'user_registration_desc', 'Allow users to create accounts.', 'السماح للمستخدمين بإنشاء حسابات.', 'Permettre aux utilisateurs de créer des comptes.', 'Benutzern erlauben, Konten zu erstellen.', 'Разрешить пользователям создавать учетные записи.', 'Kullanıcıların hesap oluşturmasına izin ver.'),
(41, 'email_validation', 'E-mail validation', 'التحقق من صحة البريد الإلكتروني', 'Validation de courrier électronique', 'E-Mail-Validierung', 'Проверка электронной почты', 'E-posta doğrulama'),
(42, 'email_validation_desc', 'Enable email validation to send activation link when user registered.', 'تمكين التحقق من صحة البريد الإلكتروني لإرسال رابط التنشيط عند تسجيل المستخدم.', 'Activer la validation du courrier électronique pour envoyer le lien d&#039;activation lorsque l&#039;utilisateur est enregistré.', 'Aktivieren Sie die E-Mail-Validierung, um den Aktivierungslink zu senden, wenn der Benutzer registriert ist.', 'Включите проверку электронной почты, чтобы отправить ссылку активации при регистрации пользователя.', 'Kullanıcı kayıtlı olduğunda etkinleştirme bağlantısı göndermek için e-posta doğrulamasını etkinleştirin.'),
(43, 'delete_account', 'Delete Account', 'حذف الحساب', 'Supprimer le compte', 'Konto löschen', 'Удалить аккаунт', 'Hesabı sil'),
(44, 'delete_account_desc', 'Allow users to delete their account.', 'السماح للمستخدمين بحذف حسابهم.', 'Autoriser les utilisateurs à supprimer leur compte.', 'Benutzern erlauben, ihr Konto zu löschen.', 'Разрешить пользователям удалять свою учетную запись.', 'Kullanıcıların hesaplarını silmelerine izin ver.'),
(45, 'save', 'Save', 'حفظ', 'sauvegarder', 'sparen', 'Сохранить', 'Kayıt etmek'),
(46, 'id', 'ID', 'هوية شخصية', 'ID', 'ICH WÜRDE', 'Я БЫ', 'İD'),
(47, 'title', 'Title', 'عنوان', 'Titre', 'Titel', 'заглавие', 'Başlık'),
(48, 'auhtor', 'Auhtor', 'Auhtor', 'Auhtor', 'Auhtor', 'Auhtor', 'Auhtor'),
(49, 'category', 'Category', 'الفئة', 'Catégorie', 'Kategorie', 'категория', 'Kategori'),
(50, 'status', 'Status', 'الحالة', 'Statut', 'Status', 'Положение дел', 'durum'),
(51, 'featured', 'Featured', 'متميز', 'En vedette', 'Vorgestellt', 'Рекомендуемые', 'Öne çıkan'),
(52, 'action', 'Action', 'عمل', 'action', 'Aktion', 'действие', 'Aksiyon'),
(53, 'manage_lists', 'Manage Lists', 'إدارة القوائم', 'Gérer les listes', 'Listen verwalten', 'Управление списками', 'Listeleri Yönet'),
(54, 'activate', 'Activate', 'تفعيل', 'Activer', 'Aktivieren', 'активировать', 'etkinleştirmek'),
(55, 'deactivate', 'Deactivate', 'عطل', 'Désactiver', 'Deaktivieren', 'дезактивировать', 'Devre dışı bırakmak'),
(56, 'yes', 'Yes', 'نعم فعلا', 'Oui', 'Ja', 'да', 'Evet'),
(57, 'no', 'No', 'لا', 'Non', 'Nein', 'нет', 'Yok hayır'),
(58, 'manage_music', 'Manage Music', 'إدارة الموسيقى', 'Gérer la musique', 'Musik verwalten', 'Управление музыкой', 'Müziği Yönet'),
(59, 'manage_news', 'Manage News', 'إدارة الأخبار', 'Gérer les nouvelles', 'Nachrichten verwalten', 'Управление новостями', 'Haberleri Yönet'),
(60, 'manage_polls', 'Manage Polls', 'إدارة استطلاعات الرأي', 'Gérer les sondages', 'Verwalten von Umfragen', 'Управление опросами', 'Anketleri Yönet'),
(61, 'site_settings', 'Site Settings', 'إعدادات الموقع', 'Paramètres du site', 'Site-Einstellungen', 'Настройки сайта', 'Site Ayarları'),
(62, 'site_name', 'Site name', 'اسم الموقع', 'Nom du site', 'Site-Name', 'Название сайта', 'Site adı'),
(63, 'site_title', 'Site title', 'عنوان الموقع', 'Titre du site', 'Seitentitel', 'Название сайта', 'Site Başlığı'),
(64, 'site_email', 'Site e-mail', 'البريد الإلكتروني للموقع', 'Site e-mail', 'Website-E-Mail', 'Электронный адрес сайта', 'Site e-postası'),
(65, 'site_keywords', 'Site keywords', 'الكلمات الرئيسية للموقع', 'Mots-clés du site', 'Site-Schlüsselwörter', 'Ключевые слова сайта', 'Site anahtar kelimeleri'),
(66, 'site_description', 'Site description', 'وصف الموقع', 'Description du site', 'Seitenbeschreibung', 'Описание сайта', 'Site Açıklaması'),
(67, 'default_language', 'Default language', 'اللغة الافتراضية', 'Langage par défaut', 'Standardsprache', 'Язык по умолчанию', 'Varsayılan dil'),
(68, 'max_upload_size', 'Max upload size', 'الحد الأقصى لحجم التحميل', 'Taille de téléchargement maxi', 'Maximale Uploadgröße', 'Максимальный размер загрузки', 'Maksimum yükleme boyutu'),
(69, 'censored_words', 'Censored Words', 'الكلمات الرقابية', 'Mots censurés', 'Zensierte Wörter', 'Цензурные слова', 'Sansürlenmiş Kelimeler'),
(70, 'censored_words_desc', 'Words to be censored, separated by a comma (,)', 'الكلمات التي تخضع للرقابة، مفصولة بفاصلة (،)', 'Les mots à censurer, séparés par une virgule (,)', 'Wörter, die zensiert werden, getrennt durch ein Komma (,)', 'Слова, которые подвергаются цензуре, разделяются запятой (,)', 'Sansürecek sözcükler virgül ile ayırılır (,)'),
(71, 'recaptcha_key', 'reCaptcha Key', 'مفتاح ريكابتشا', 'ReCaptcha Key', 'ReCaptcha Schlüssel', 'Ключ reCaptcha', 'ReCaptcha Anahtarı'),
(72, 'recaptcha', 'reCaptcha', 'اختبار ReCAPTCHA', 'ReCaptcha', 'ReCaptcha', 'ReCaptcha', 'ReCaptcha'),
(73, 'google_code', 'Google analytics code', 'شفرة تحليلات غوغل', 'Code Google Analytics', 'Google Analytics-Code', 'Код Google Analytics', 'Google analytics kodu'),
(74, 'social_login', 'Social Login', 'تسجيل الدخول الاجتماعي', 'Connexion sociale', 'Soziale Anmeldung', 'Социальный вход', 'Sosyal giriş'),
(75, 'terms_pages', 'Terms Pages', 'صفحات الصفحات', 'Pages de termes', 'Begriffe Seiten', 'Условия Страницы', 'Şartlar Sayfaları'),
(76, 'username', 'Username', 'اسم المستخدم', 'Nom d&#039;utilisateur', 'Benutzername', 'имя пользователя', 'Kullanıcı adı'),
(77, 'email', 'Email', 'البريد الإلكتروني', 'Email', 'Email', 'Эл. адрес', 'E-posta'),
(78, 'ip_address', 'IP Address', 'عنوان بروتوكول الإنترنت', 'Adresse IP', 'IP Adresse', 'Айпи адрес', 'IP adresi'),
(79, 'reset_your_password', 'Reset your password', 'اعد ضبط كلمه السر', 'réinitialisez votre mot de passe', 'Setze dein Passwort zurück', 'Сбросить пароль', 'Şifrenizi sıfırlayın'),
(80, 'create_new', 'Create new', 'خلق جديد إبداع جديد', 'Créer un nouveau', 'Erstelle neu', 'Создавать новое', 'Yeni oluşturmak'),
(81, 'delete_post', 'Delete Post', 'حذف آخر', 'Supprimer le message', 'Beitrag entfernen', 'Удалить сообщение', 'Postayı Sil'),
(82, 'edit_post', 'Edit Post', 'تعديل المنشور', 'Modifier la poste', 'Beitrag bearbeiten', 'Редактировать сообщение', 'Gönderiyi düzenle'),
(83, 'forgot_password', 'Forgot Password', 'هل نسيت كلمة المرور', 'Mot de passe oublié', 'Passwort vergessen', 'Забыли пароль', 'Parolanızı mı unuttunuz'),
(84, 'home', 'Home', 'الصفحة الرئيسية', 'Accueil', 'Zuhause', 'Главная', 'Ev'),
(85, 'latest_lists', 'Latest Lists', 'أحدث القوائم', 'Dernières listes', 'Neueste Listen', 'Последние списки', 'Son Listeler'),
(86, 'latest_music', 'Latest Music', 'أحدث الموسيقى', 'Dernière musique', 'Neueste Musik', 'Последняя музыка', 'Son Müzik'),
(87, 'latest_news', 'Latest News', 'أحدث الأخبار', 'Dernières nouvelles', 'Neuesten Nachrichten', 'Последние новости', 'Son Haberler'),
(88, 'latest_polls', 'Latest Polls', 'آخر استطلاعات الرأي', 'Derniers sondages', 'Letzte Umfragen', 'Последние опросы', 'Son Anketler'),
(89, 'latest_videos', 'Latest Videos', 'أحدث مقاطع الفيديو', 'Dernières vidéos', 'Neueste Videos', 'Последние видео', 'En Yeni Videolar'),
(90, 'login', 'Login', 'تسجيل الدخول', 'S&#039;identifier', 'Anmeldung', 'Авторизоваться', 'Oturum aç'),
(91, 'create_new_account', 'Create new account', 'انشاء حساب جديد', 'Créer un nouveau compte', 'Neuen Account erstellen', 'Создать новый аккаунт', 'Yeni hesap oluştur'),
(92, 'saved_drafts', 'Saved drafts', 'المسودات المحفوظة', 'Traites enregistrées', 'Gespeicherte Entwürfe', 'Сохраненные черновики', 'Taslaklar kaydedildi'),
(93, 'search', 'Search', 'بحث', 'Chercher', 'Suche', 'Поиск', 'Arama'),
(94, 'settings', 'Settings', 'إعدادات', 'Paramètres', 'Einstellungen', 'настройки', 'Ayarlar'),
(95, 'tags', 'Tags', 'الكلمات', 'Mots clés', 'Tags', 'Теги', 'Etiketler'),
(96, 'terms_of_use', 'Terms of use', 'تعليمات الاستخدام', 'Conditions d&#039;utilisation', 'Nutzungsbedingungen', 'Условия эксплуатации', 'Kullanım Şartları'),
(97, 'about_us', 'About us', 'معلومات عنا', 'À propos de nous', 'Über uns', 'О нас', 'Hakkımızda'),
(98, 'privacy_policy', 'Privacy Policy', 'سياسة الخصوصية', 'Politique de confidentialité', 'Datenschutz-Bestimmungen', 'политика конфиденциальности', 'Gizlilik Politikası'),
(99, 'facebook_post', 'Facebook Post', 'الفيسبوك المشاركة', 'Message Facebook', 'Facebook Post', 'Сообщение', 'Facebook Post'),
(100, 'get_post', 'Get Post', 'الحصول على المشاركة', 'Obtenir un message', 'Holen Sie sich Post', 'Получить сообщение', 'Mesaj Gönder'),
(101, 'title_optional', 'Title (Optinal)', 'العنوان (أوبتينال)', 'Titre (Optinal)', 'Titel (Optinal)', 'Название (Optinal)', 'Başlık (Optinal)'),
(102, 'fb_url', 'Facebook Post URL', 'فاسيبوك بوست ورل', 'Facebook Post URL', 'Facebook Post URL', 'URL-адрес Facebook', 'Facebook Post URL'),
(103, 'image', 'Image', 'صورة', 'Image', 'Bild', 'Образ', 'görüntü'),
(104, 'choose_img', 'Choose Image', 'اختر صورة', 'Choisir l&#039;image', 'Bild auswählen', 'Выберите изображение', 'Resim Seç'),
(105, 'fetch_url', 'Fetch URL', 'جلب عنوان ورل', 'URL de recherche', 'URL abrufen', 'Получить URL-адрес', 'URLyi getir'),
(106, 'fetch_url_from_image', 'Fetch image from URL', 'جلب صورة من عنوان ورل', 'Recherche de l&#039;image à partir de l&#039;URL', 'Bild von URL abrufen', 'Получить изображение из URL-адреса', 'URLyi resimden getir'),
(107, 'ig', 'Instagram', 'إينستاجرام', 'Instagram', 'Instagram', 'Instagram', 'Instagram'),
(108, 'ig_post_url', 'Instagram Post URL', 'إينستاجرام بوست ورل', 'Instagram Post URL', 'Instagram Post URL', 'URL-адрес страницы Instagram', 'Instagram Post URL'),
(109, 'poll', 'Poll', 'تصويت', 'Sondage', 'Umfrage', 'Голосование', 'Anket'),
(110, 'question', 'Question', 'سؤال', 'Question', 'Frage', 'Вопрос', 'Soru'),
(111, 'img_link', 'Image Link', 'رابط الصورة', 'Image Link', 'Bild Link', 'Ссылка на изображение', 'Resim Bağlantısı'),
(112, 'add_answer', 'Add answer', 'أضف إجابة', 'Ajouter une réponse', 'Antwort hinzufügen', 'Добавить ответ', 'Cevap ekle'),
(113, 'answer', 'Answer', 'إجابة', 'Répondre', 'Antworten', 'Ответ', 'Cevap'),
(114, 'soundcloud', 'Soundcloud', 'SoundCloud لل', 'Soundcloud', 'Soundcloud', 'Soundcloud', 'ses bulutu'),
(115, 'soundcloud_url', 'Soundcloud URL', 'سوندكلود ورل', 'URL de Soundcloud', 'Soundcloud URL', 'URL-адрес Soundcloud', 'Sesli arama URLsi'),
(116, 'get_music', 'Get Music', 'الحصول على الموسيقى', 'Obtenir de la musique', 'Holen Sie sich Musik', 'Получить музыку', 'Müzik Al'),
(117, 'text', 'Text', 'نص', 'Texte', 'Text', 'Текст', 'Metin'),
(118, 'tweet', 'Tweet', 'سقسقة', 'Tweet', 'Tweet', 'чирикать', 'Cıvıldamak'),
(119, 'tweet_url', 'Tweet URL', 'تويت ورل', 'Tweet URL', 'Tweet URL', 'URL Tweet', 'Tweet URL'),
(120, 'get_tweet', 'Get Tweet', 'الحصول على تويت', 'Obtenir Tweet', 'Holen Sie sich Tweet', 'Получить твит', 'Tweet alın'),
(121, 'video', 'Video', 'فيديو', 'Vidéo', 'Video', 'видео', 'Video'),
(122, 'video_supported_sites', 'Supported sites: Youtube, Facebook, Dailymotion, Vimeo, Vine', 'المواقع المدعومة: يوتيوب، الفيسبوك، دايلي موشن، فيميو، الكرمة', 'Sites supportés: Youtube, Facebook, Dailymotion, Vimeo, Vine', 'Unterstützte Seiten: Youtube, Facebook, Dailymotion, Vimeo, Vine', 'Поддерживаемые сайты: Youtube, Facebook, Dailymotion, Vimeo, Vine', 'Desteklenen siteler: Youtube, Facebook, Dailymotion, Vimeo, Vine'),
(123, 'video_url', 'Video URL', 'عنوان الفيديو', 'URL de la vidéo', 'Video-URL', 'URL видео', 'Video URLsi'),
(124, 'details', 'Details', 'تفاصيل', 'Détails', 'Details', 'Детали', 'ayrıntılar'),
(125, 'type_a_title', 'Type a title', 'اكتب عنوانا', 'Tapez un titre', 'Geben Sie einen Titel ein', 'Введите заголовок', 'Başlık yazın'),
(126, 'short_title', 'Short Title', 'عنوان قصير', 'Titre court', 'Kurzer Titel', 'Короткое название', 'Kısa başlık'),
(127, 'max_36_c', 'Max 36 characters', 'الحد الأقصى 36 حرفا', 'Max 36 caractères', 'Maximal 36 Zeichen', 'Макс. 36 символов', 'Maks 36 karakter'),
(128, 'desc', 'Description', 'وصف', 'La description', 'Beschreibung', 'Описание', 'Açıklama'),
(129, 'type_a_desc', 'Type a Description', 'اكتب وصفا', 'Tapez une description', 'Geben Sie eine Beschreibung ein', 'Введите описание', 'Açıklama yazın'),
(130, 'content_and_entries', 'Content &amp; Entries', 'المحتوى والإدخالات', 'Contenu et entrées', 'Inhalt &amp; Einträge', 'Содержание и записи', 'İçerik ve Girişler'),
(131, 'add_new_entry', 'Add new entry', 'إضافة إدخال جديد', 'Ajouter une nouvelle entrée', 'Neuen Eintrag hinzufügen', 'Добавить новую запись', 'Yeni giriş ekle'),
(132, 'option', 'Option', 'اختيار', 'Option', 'Option', 'вариант', 'seçenek'),
(133, 'p_img', 'Preview Image', 'معاينة الصورة', 'Image d&#039;aperçu', 'Vorschaubild', 'Предварительный просмотр', 'Önizleme Resmi'),
(134, 'publish', 'Publish', 'نشر', 'Publier', 'Veröffentlichen', 'Публиковать', 'yayınlamak'),
(135, 'entries_per_page', 'Entries per page', 'الإدخالات في كل صفحة', 'Entrées par page', 'Einträge pro Seite', 'Записи на страницу', 'Sayfa başı giriş'),
(136, 'all', 'All', 'الكل', 'Tout', 'Alle', 'Все', 'Herşey'),
(137, 'save_as_draft_btn', 'Save as draft &amp; Preview', 'حفظ كمسودة والمعاينة', 'Enregistrer sous forme de brouillon et Aperçu', 'Speichern als Entwurf und Vorschau', 'Сохранить как черновик и предварительный просмотр', 'Taslak olarak kaydet ve Önizle'),
(138, 'cancel', 'Cancel', 'إلغاء', 'Annuler', 'Stornieren', 'Отмена', 'İptal etmek'),
(139, 'please_wait', 'Please wait...', 'أرجو الإنتظار...', 'S&#039;il vous plaît, attendez...', 'Warten Sie mal...', 'Пожалуйста, подождите...', 'Lütfen bekle...'),
(140, 'list', 'List', 'قائمة', 'liste', 'Liste', 'Список', 'Liste'),
(141, 'delete_confirm', 'Are you sure that you want to delete this post?', 'هل تريد بالتأكيد حذف هذه المشاركة؟', 'Êtes-vous sûr de vouloir supprimer cette publication?', 'Bist du sicher, dass du diesen Beitrag löschen möchtest?', 'Вы уверены, что хотите удалить этот пост?', 'Bu yayını silmek istediğinizden emin misiniz?'),
(142, 'delete_yes', 'Yes, delete everyting', 'نعم، حذف إيفريتينغ', 'Oui, supprimez tout', 'Ja, alles löschen', 'Да, удалите все', 'Evet, hepsini sil'),
(143, 'delete_no', 'No, i have changed my mind', 'لا، لقد غيرت ذهني', 'Non, j&#039;ai changé d&#039;avis', 'Nein, ich habe meine Meinung geändert', 'Нет, я передумал', 'Hayır, fikrimi değiştirdim.'),
(144, 'set_as_p_image', 'Set as preview image', 'تعيين كصورة المعاينة', 'Définir comme image daperçu', 'Als Vorschaubild setzen', 'Установить как изображение предварительного просмотра', 'Önizleme resmi olarak ayarla'),
(145, 'voted_error', 'Voted options are not modifiable, you can still add new options.', 'خيارات التصويت ليست قابلة للتعديل، لا يزال بإمكانك إضافة خيارات جديدة.', 'Les options votées ne sont pas modifiables, vous pouvez encore ajouter de nouvelles options.', 'Abgestimmte Optionen sind nicht modifizierbar, Sie können noch neue Optionen hinzufügen.', 'Параметры голосового доступа не изменяются, вы можете добавлять новые параметры.', 'Oylanan seçenekler değiştirilemez, yine de yeni seçenekler ekleyebilirsiniz.'),
(146, 'save_and_publish', 'Save &amp; Publish', 'حفظ ونشر', 'Enregistrer et publier', 'Speichern und veröffentlichen', 'Сохранить и опубликовать', 'Kaydet ve Yayınla'),
(147, 'total_votes', 'Total Votes', 'مجموع الأصوات', 'Total des votes', 'Gesamte stimmen', 'Всего голосов', 'Toplam Oy'),
(148, 'copyright', 'Copyright © 2020', 'كوبيرايت © 2017', 'Copyright © 2017', 'Copyright © 2017', 'Copyright © 2017', 'Telif Hakkı © 2017'),
(149, 'all_right', 'All rights reserved', 'كل الحقوق محفوظة', 'Tous les droits sont réservés', 'Alle Rechte vorbehalten', 'Все права защищены', 'Her hakkı saklıdır'),
(150, 'email_address', 'E-mail address', 'عنوان البريد الإلكتروني', 'Adresse e-mail', 'E-Mail-Addresse', 'Адрес электронной почты', 'E'),
(151, 'request_new_password', 'Request New Password', 'طلب كلمة مرور جديدة', 'Demander un nouveau mot de passe', 'Fordere ein neues Passwort an', 'Запросить новый пароль', 'Yeni şifre isteği'),
(152, 'got_your_password', 'Got your password?', 'هل حصلت على كلمة المرور؟', 'Vous avez votre mot de passe?', 'Haben Sie Ihr Passwort?', 'Получил пароль?', 'Şifreniz var mı?'),
(153, 'email_has_sent', 'Email has been sent', 'تم ارسال البريد الالكتروني', 'L&#039;email a été envoyé', 'Die Email wurde verschickt', 'Письмо было отправлено', 'Mail gönderildi'),
(154, 'search_keyword', 'Search for news, lists, videos or polls..', 'البحث عن الأخبار، وقوائم، وأشرطة الفيديو أو استطلاعات الرأي ..', 'Rechercher des nouvelles, des listes, des vidéos ou des sondages ..', 'Suche nach Neuigkeiten, Listen, Videos oder Umfragen.', 'Поиск новостей, списков, видеороликов или опросов.', 'Haber aramak, listeler, videolar veya anketler yapmak ..'),
(155, 'my_profile', 'My Profile', 'ملفي', 'Mon profil', 'Mein Profil', 'Мой профайл', 'Benim profilim'),
(156, 'edit', 'Edit', 'تصحيح', 'modifier', 'Bearbeiten', 'редактировать', 'Düzenleme'),
(157, 'admin_panel', 'Admin Panel', 'لوحة الادارة', 'panneau d&#039;administration', 'Administrationsmenü', 'Панель администратора', 'Admin Paneli'),
(158, 'logout', 'Log out', 'الخروج', 'Connectez - Out', 'Ausloggen', 'Выйти', 'Çıkış Yap'),
(159, 'create_new_post', 'Create new post', 'إنشاء مشاركة جديدة', 'Créer une nouvelle publication', 'Neuen Beitrag erstellen', 'Создать новую должность', 'Yeni mesaj oluştur'),
(160, 'register', 'Register', 'تسجيل', 'registre', 'Neu registrieren', 'регистр', 'Kayıt olmak'),
(161, 'most_watched', 'Most Watched', 'الأكثر مشاهدة', 'Les plus regardés', 'Meist gesehen', 'Самые просматриваемые', 'En Çok İzlenen'),
(162, 'top_news', 'Top News', 'الاخبار المهمه', 'Actualité à la Une', 'Top News', 'Главные новости', 'En Çok Okunan Haberler'),
(163, 'best_video_this_week', 'Best Video This Week', 'أفضل فيديو هذا الأسبوع', 'Meilleure vidéo cette semaine', 'Das beste Video dieser Woche', 'Лучшее видео на этой неделе', 'Bu Hafta En İyi Video'),
(164, 'trending', 'Trending', 'الشائع', 'Tendances', 'Trending', 'Trending', 'Trend'),
(165, 'facebook', 'Facebook', 'فيس بوك', 'Facebook', 'Facebook', 'facebook', 'Facebook'),
(166, 'google_plus', 'Google+', 'في + Google', 'Google+', 'Google+', 'Google+', 'Google+'),
(167, 'wowonder', 'Wowonder', 'Wowonder', 'Wowonder', 'Wowonder', 'Wowonder', 'Wowonder'),
(168, 'reddit', 'Reddit', 'رديت', 'Reddit', 'Reddit', 'Reddit', 'Reddit'),
(169, 'twitter', 'Twitter', 'تغريد', 'Gazouillement', 'Twitter', 'щебет', 'heyecan'),
(170, 'posted', 'Posted', 'نشر', 'Publié', 'Gesendet', 'Сообщение', 'Gönderildi'),
(171, 'by', 'By', 'بواسطة', 'Par', 'Durch', 'От', 'Tarafından'),
(172, 'no_posts_to_show', 'No posts to show', 'لا توجد مشاركات لعرضها', 'Aucun message à afficher', 'Keine Beiträge zu zeigen', 'Нет сообщений для отображения', 'Göstermek için hiç mesaj yok'),
(173, 'load_more_lists', 'Load more lists', 'تحميل المزيد من القوائم', 'Chargez plus de listes', 'Laden Sie weitere Listen', 'Загрузите больше списков', 'Daha fazla liste yükle'),
(174, 'load_more_music', 'Load more music', 'تحميل المزيد من الموسيقى', 'Chargez plus de musique', 'Laden Sie mehr Musik', 'Загрузите больше музыки', 'Daha fazla müzik yükle'),
(175, 'load_more_news', 'Load more news', 'تحميل المزيد من الأخبار', 'Chargez plus d&#039;actualités', 'Laden Sie mehr Nachrichten', 'Загрузите еще новости', 'Daha fazla haber yükle'),
(176, 'load_more_polls', 'Load more polls', 'تحميل المزيد من استطلاعات الرأي', 'Charger plus de sondages', 'Laden Sie mehr Umfragen', 'Загрузить больше опросов', 'Daha fazla anket yükle'),
(177, 'load_more_videos', 'Load more videos', 'تحميل المزيد من أشرطة الفيديو', 'Chargez plus de vidéos', 'Laden Sie weitere Videos', 'Загрузить больше видео', 'Daha fazla video yükle'),
(178, 'categories', 'Categories', 'الاقسام', 'Catégories', 'Kategorien', 'категории', 'Kategoriler'),
(179, 'login_to_account', 'Login to your account', 'تسجيل الدخول إلى حسابك', 'Connectez-vous à votre compte', 'Melde dich in deinem Konto an', 'Войдите в свой аккаунт', 'hesabınıza giriş yapın'),
(180, 'password', 'Password', 'كلمه السر', 'Mot de passe', 'Passwort', 'пароль', 'Parola'),
(181, 'forgot_password_mark', 'Forgot your password', 'نسيت رقمك السري', 'Mot de passe oublié', 'Haben Sie Ihr Passwort vergessen', 'Забыли пароль', 'Parolanızı mı unuttunuz'),
(182, 'or_connect_with', 'Or connect with', 'أو التواصل مع', 'Ou connectez-vous avec', 'Oder mit zu verbinden', 'Или подключиться', 'Veya bağlan'),
(183, 'create_account', 'Create account', 'إصنع حساب', 'Créer un compte', 'Benutzerkonto anlegen', 'Регистрация', 'Hesap açmak'),
(184, 'new_here', 'New here?', 'جديد هنا؟', 'Nouveau ici?', 'Neu hier?', 'Новенький тут?', 'Burada yeni?'),
(185, 'redirecting', 'Redirecting..', 'إعادة توجيه..', 'Redirection ...', 'Umleitung ..', 'Перенаправление ..', 'Yönlendiriliyor ..'),
(186, 'online', 'Online', 'عبر الانترنت', 'En ligne', 'Online', 'В сети', 'İnternet üzerinden'),
(187, 'c_password', 'Confirm Password', 'تأكيد كلمة المرور', 'Confirmez le mot de passe', 'Bestätige das Passwort', 'Подтвердите Пароль', 'Şifreyi Onayla'),
(188, 'sign_up', 'Sign Up!', 'سجل!', 'S&#039;inscrire!', 'Anmelden!', 'Зарегистрироваться!', 'Kaydol!'),
(189, 'already_registered', 'Already registered?', 'مسجل بالفعل؟', 'Déjà enregistré?', 'Bereits registriert?', 'Уже зарегистрирован?', 'Zaten kayıtlı?'),
(190, 'new_password', 'New password', 'كلمة السر الجديدة', 'Nouveau mot de passe', 'Neues Kennwort', 'Новый пароль', 'Yeni Şifre'),
(191, 'confirm_new_password', 'Confirm new password', 'تأكيد كلمة المرور الجديدة', 'Confirmer le nouveau mot de passe', 'Bestätige neues Passwort', 'Подтвердите новый пароль', 'Yeni şifreyi onayla'),
(192, 'reset_password', 'Reset Password', 'إعادة تعيين كلمة المرور', 'réinitialiser le mot de passe', 'Passwort zurücksetzen', 'Сброс пароля', 'Şifreyi yenile'),
(193, 'remember_your_old_password', 'Do you remember your old password?', 'هل تتذكر كلمة المرور القديمة؟', 'Vous souvenez-vous de votre ancien mot de passe?', 'Erinnerst du dich an dein altes Passwort?', 'Вы помните свой старый пароль?', 'Eski şifrenizi hatırlıyor musunuz?'),
(194, 'your_password_has_been_reset', 'Your password has been reset, please wait..', 'تمت إعادة تعيين كلمة المرور، يرجى الانتظار.', 'Votre mot de passe a été réinitialisé, veuillez patienter ...', 'Dein Passwort wurde zurückgesetzt, bitte warte ..', 'Ваш пароль был сброшен, подождите.', 'Şifreniz sıfırlandı, lütfen bekleyin ..'),
(195, 'load_more_posts', 'Load more posts', 'تحميل المزيد من المشاركات', 'Chargez plus de publications', 'Laden Sie mehr Beiträge', 'Загрузка дополнительных сообщений', 'Daha fazla yayın yükle'),
(196, 'active', 'Active', 'نشيط', 'actif', 'Aktiv', 'активный', 'Aktif'),
(197, 'inactive', 'Inactive', 'غير نشط', 'Inactif', 'Inaktiv', 'Неактивный', 'etkisiz'),
(198, 'type', 'Type', 'اكتب', 'Type', 'Art', 'Тип', 'tip'),
(199, 'user', 'User', 'المستعمل', 'Utilisateur', 'Benutzer', 'пользователь', 'kullanıcı'),
(200, 'admin', 'Admin', 'مشرف', 'Admin', 'Administrator', 'Администратор', 'yönetim'),
(201, 'avatar_and_cover', 'Avatar &amp; Cover', 'الصورة الرمزية والغطاء', 'Avatar et couverture', 'Avatar &amp; Cover', 'Аватар &amp; Cover', 'Avatar &amp; Kapak'),
(202, 'avatar', 'Avatar', 'الصورة الرمزية', 'Avatar', 'Benutzerbild', 'Аватар', 'Avatar'),
(203, 'cover', 'Cover', 'غطاء، يغطي', 'Couverture', 'Abdeckung', 'Обложка', 'kapak'),
(204, 'general', 'General', 'جنرال لواء', 'Général', 'General', 'Генеральная', 'Genel'),
(205, 'profile', 'Profile', 'الملف الشخصي', 'Profil', 'Profil', 'Профиль', 'Profil'),
(206, 'current_passowrd', 'Current Password', 'كلمة السر الحالية', 'Mot de passe actuel', 'Aktuelles Passwort', 'текущий пароль', 'Şimdiki Şifre'),
(207, 'country', 'Country', 'بلد', 'Pays', 'Land', 'Страна', 'ülke'),
(208, 'gender', 'Gender', 'جنس', 'Le genre', 'Geschlecht', 'Пол', 'Cinsiyet'),
(209, 'password_settings', 'Password Settings', 'إعدادات كلمة المرور', 'Paramètres du mot de passe', 'Kennworteinstellungen', 'Настройки пароля', 'Şifre Ayarları'),
(210, 'profile_settings', 'Profile Settings', 'إعدادات الملف الشخصي', 'Paramètres de profil', 'Profileinstellungen', 'Настройки профиля', 'Profil ayarları'),
(211, 'first_name', 'First Name', 'الاسم الاول', 'Prénom', 'Vorname', 'Имя', 'İsim'),
(212, 'last_name', 'Last Name', 'الكنية', 'Nom de famille', 'Familienname, Nachname', 'Фамилия', 'Soyadı'),
(213, 'about_profile', 'About', 'حول', 'Sur', 'Über', 'Около', 'hakkında'),
(214, 'more_news', 'More news', 'المزيد من الأخبار', 'Plus de nouvelles', 'Mehr Nachrichten', 'Другие новости', 'Daha fazla haber'),
(215, 'more_lists', 'More lists', 'المزيد من القوائم', 'Plus de listes', 'Weitere Listen', 'Больше списков', 'Diğer listeler'),
(216, 'more_videos', 'More videos', 'فيديوهات اكثر', 'Plus de vidéos', 'Mehr Videos', 'Другие видеоролики', 'Daha fazla video'),
(217, 'more_music', 'More Music', 'المزيد من الموسيقى', 'Plus de musique', 'Mehr Musik', 'Больше музыки', 'Daha Fazla Müzik'),
(218, 'more_polls', 'More Polls', 'المزيد من الاستطلاعات', 'Plus de sondages', 'Weitere Umfragen', 'Другие опросы', 'Daha Fazla Anket'),
(219, 'stay_in_contact', 'Stay in contact', 'ابقى على اتصال', 'Rester en contact', 'Bleiben Sie in Kontakt', 'Оставайся на связи', 'İletişimde kal'),
(220, 'your_facebook_page', 'Your facebook page, for sidebar', 'صفحة الفيسبوك الخاص بك، الشريط الجانبي', 'Votre page Facebook, pour la barre latérale', 'Ihre Facebook-Seite, für Seitenleiste', 'Ваша страница на facebook, для боковой панели', 'Kenar çubuğu için facebook sayfanız'),
(221, 'updated', 'updated', 'محدث', 'actualisé', 'aktualisiert', 'обновленный', 'güncellenmiş'),
(222, 'in', 'in', 'في', 'dans', 'im', 'в', 'içinde'),
(223, 'views', 'Views', 'الآراء', 'Vues', 'Ansichten', 'Просмотры', 'Görünümler'),
(224, 'share', 'Share', 'شارك', 'Partager', 'Aktie', 'Поделиться', 'Pay'),
(225, 'shares', 'Shares', 'تشارك', 'Actions', 'Anteile', 'Акции', 'Paylar'),
(226, 'tag', 'Tag', 'بطاقة', 'Marque', 'Tag', 'Тег', 'Etiket'),
(227, 'pending', 'Pending', 'قيد الانتظار', 'en attendant', 'steht aus', 'в ожидании', 'kadar'),
(228, 'next_page', 'Next Page', 'الصفحة التالية', 'Page suivante', 'Folgeseite', 'Следующая Страница', 'Sonraki Sayfa'),
(229, 'previous_page', 'Previous Page', 'الصفحة السابقة', 'Page précédente', 'Vorherige Seite', 'Предыдущая страница', 'Önceki sayfa'),
(230, 'waiting_for_approval', 'Waiting for approval', 'بانتظار الموافقة', 'en attente d&#039;approbation', 'warten auf die Bestätigung', 'Ожидание подтверждения', 'onay bekleniyor'),
(231, 'email_exists', 'This e-mail is already in use', 'هذا البريد استخدم من قبل', 'Cet e-mail est déjà utilisée', 'Diese E-Mail-Adresse wird schon verwendet', 'Этот электронный адрес уже используется', 'Bu e-posta zaten kullanılıyor'),
(232, 'username_exists', 'Username is already exists.', 'اسم المستخدم موجود من قبل.', 'Le nom d&#039;utilisateur existe déjà.', 'Benutzername ist bereits vorhanden.', 'Имя пользователя уже существует.', 'Kullanıcı adı zaten var.'),
(233, 'username_invalid_characters', 'Invalid username characters', 'أحرف اسم المستخدم غير صالحة', 'Caractères d&#039;identifiant invalides', 'Ungültige Benutzernamen', 'Неверные символы имени пользователя', 'Geçersiz kullanıcı adı karakterleri'),
(234, 'email_invalid_characters', 'This e-mail is invalid.', 'هذا البريد الإلكتروني غير صالح.', 'Cet e-mail est invalide.', 'Diese E-Mail ist ungültig.', 'Это сообщение недействительно.', 'Bu e-posta geçersiz.'),
(235, 'username_characters_length', 'Username must be between 5 / 32', 'يجب أن يكون اسم المستخدم بين 5/32', 'Le nom d&#039;utilisateur doit être compris entre 5/32', 'Benutzername muss zwischen 5/32 liegen', 'Имя пользователя должно быть от 5/32', 'Kullanıcı adı 5/32 arasında olmalıdır'),
(236, 'reCaptcha_error', 'Please Check the re-captcha.', 'يرجى التحقق من إعادة كابتشا.', 'Vérifiez le re-captcha.', 'Bitte überprüfen Sie das Re-Captcha.', 'Проверьте правильность перехвата.', 'Lütfen yeniden captchayı kontrol edin.'),
(237, 'setting_updated', 'Setting successfully updated !', 'تم تحديث الإعداد بنجاح!', 'Mise à jour réussie!', 'Einstellung erfolgreich aktualisiert!', 'Настройка успешно обновлена!', 'Ayar başarıyla güncellendi!'),
(238, 'incorrect_username_or_password', 'Incorrect username or password', 'اسم المستخدم أو كلمة المرور غير صحيحة', 'identifiant ou mot de passe incorrect', 'Falscher Benutzername oder Passwort', 'Неверное имя пользователя или пароль', 'Yanlış kullanıcı adı ya da parola'),
(239, 'your_account_is_disabled', 'Your account is disabled, please contact us for more info.', 'تم تعطيل حسابك، يرجى الاتصال بنا للحصول على مزيد من المعلومات.', 'Votre compte est désactivé, veuillez nous contacter pour plus d&#039;informations.', 'Ihr Konto ist deaktiviert, bitte kontaktieren Sie uns für weitere Informationen.', 'Ваша учетная запись отключена, свяжитесь с нами для получения дополнительной информации.', 'Hesabınız devre dışı, daha fazla bilgi için lütfen bizimle iletişime geçin.'),
(240, 'please_check_details', 'Please check your details', 'الرجاء مراجعة التفاصيل الخاصة بك', 'Vérifiez vos coordonnées', 'Bitte überprüfe deine Details', 'Пожалуйста, проверьте свои данные', 'Lütfen ayrıntılarınızı kontrol edin'),
(241, 'username_already_taken', 'Username is already taken', 'اسم المستخدم مسجل بالفعل', 'Nom d&#039;utilisateur déjà pris', 'Benutzername ist bereits vergeben', 'Имя пользователя уже используется', 'Kullanıcı adı zaten alınmış'),
(242, 'password_is_too_short', 'Password is too short', 'كلمة المرور قصيرة جدا', 'Le mot de passe est trop court', 'Das Passwort ist zu kurz', 'Пароль слишком короткий', 'Şifre çok kısa'),
(243, 'password_not_match', 'Password not match', 'كلمة المرور غير متطابقة', 'Le mot de passe ne correspond pas', 'Passwort nicht übereinstimmen', 'Пароль не соответствует', 'Şifre eşleşmiyor'),
(244, 'successfully_joined', 'Successfully joined, Please wait..', 'تم الانضمام بنجاح، يرجى الانتظار ..', 'Rejoint avec succès, Veuillez patienter ...', 'Erfolgreich beigetreten, bitte warte ..', 'Успешно присоединился, пожалуйста, подождите ..', 'Başarıyla katıldı, lütfen bekleyin ..'),
(245, 'successfully_joined_desc', 'Registration successful! We have sent you an email, Please check your inbox/spam to verify your email.', 'التسجيل بنجاح! لقد أرسلنا إليك رسالة إلكترونية، يرجى التحقق من البريد الوارد / الرسائل غير المرغوب فيها للتحقق من بريدك الإلكتروني.', 'Inscription réussi! Nous vous avons envoyé un courriel, vérifiez votre boîte de réception / spam pour vérifier votre courrier électronique.', 'Registrierung erfolgreich! Wir haben Ihnen eine E-Mail geschickt, bitte überprüfen Sie Ihren Posteingang / Spam, um Ihre E-Mail zu bestätigen.', 'Регистрация прошла успешно! Мы отправили вам электронное письмо. Пожалуйста, проверьте свой почтовый ящик / спам, чтобы подтвердить свою электронную почту.', 'Kayıt başarılı! Size bir e-posta gönderdik, e-postanızı doğrulamak için lütfen gelen kutunuzu / spaminizi kontrol edin.'),
(246, 'please_fill_info', 'Please fill the required info', 'يرجى ملء المعلومات المطلوبة', 'Remplissez les informations requises', 'Bitte füllen Sie die benötigten Informationen aus', 'Пожалуйста, заполните необходимую информацию', 'Lütfen gerekli bilgileri doldurun'),
(247, 'email_not_exist', 'Email is not exist', 'البريد الإلكتروني غير موجود', 'Le courrier électronique n&#039;existe pas', 'E-Mail ist nicht vorhanden', 'Электронная почта не существует', 'E-posta var değil'),
(248, 'error_found_request', 'Error found while proccesing your request, please try again later.', 'حدث خطأ أثناء إجراء الطلب المسبق لطلبك، الرجاء إعادة المحاولة لاحقا.', 'Erreur trouvée lors de la recherche de votre demande, essayez à nouveau plus tard.', 'Fehler bei der Beschaffung Ihrer Anfrage gefunden, bitte versuchen Sie es später erneut.', 'Ошибка при выполнении вашего запроса, повторите попытку позже.', 'İsteğiniz işleme devam ederken bir hata bulundu, lütfen daha sonra tekrar deneyin.'),
(249, 'invalid_reset_code', 'Invalid reset code', 'رمز إعادة التعيين غير صالح', 'Code de réinitialisation invalide', 'Ungültiger Reset-Code', 'Неверный код возврата', 'Geçersiz sıfırlama kodu'),
(250, 'error_found_please_try_again_later', 'Error found, please try again later', 'حدث خطأ، يرجى إعادة المحاولة لاحقا', 'Une erreur a été trouvée, réessayez plus tard', 'Fehler gefunden, bitte später nochmal versuchen', 'Обнаружена ошибка, повторите попытку позже', 'Hata bulundu, lütfen daha sonra tekrar deneyin'),
(251, 'title_is_required', 'Title is required', 'العنوان مطلوب', 'Le titre est requis', 'Titel ist erforderlich', 'Требуется название', 'Başlık gereklidir'),
(252, 'short_title_is_required', 'Short title is required', 'مطلوب عنوان قصير', 'Un titre court est requis', 'Kurzer Titel ist erforderlich', 'Требуется короткое название', 'Kısa başlık gereklidir'),
(253, 'max_allowed_ch_short_title', 'Max allowed characters is 36', 'الحد الأقصى المسموح به هو 36 حرفا', 'Les caractères maximum autorisés sont 36', 'Max erlaubt Zeichen ist 36', 'Максимально допустимые символы: 36', 'İzin verilen maksimum karakter sayısı 36'),
(254, 'min_allowed_ch_short_title', 'Minimum allowed characters is 5', 'الحد الأدنى المسموح به هو 5', 'Les caractères minimum autorisés sont 5', 'Minimal zulässige Zeichen ist 5', 'Минимальные допустимые символы: 5', 'İzin verilen minimum karakter sayısı 5'),
(255, 'desc_is_required', 'Description is required', 'الوصف مطلوب', 'Description requise', 'Beschreibung ist erforderlich', 'Требуется описание', 'Açıklama gerekli'),
(256, 'tage_are_required', 'Tags are required', 'العلامات مطلوبة', 'Les balises sont requises', 'Tags sind erforderlich', 'Требуются теги', 'Etiketler gereklidir'),
(257, 'category_is_required', 'Category is required', 'الفئة مطلوبة', 'Catégorie requise', 'Kategorie ist erforderlich', 'Категория обязательна', 'Kategori gerekli'),
(258, 'preview_image_is_required', 'Preview image is required', 'معاينة الصورة مطلوبة', 'L&#039;image d&#039;aperçu est requise', 'Vorschaubild ist erforderlich', 'Предварительный просмотр изображения требуется', 'Önizleme resmi gerekiyor'),
(259, 'error_not_supported_video', 'Error, not supported video url or the video not found.', 'خطأ، ورل الفيديو غير معتمد أو لم يتم العثور على الفيديو.', 'Erreur, URL vidéo non prise en charge ou vidéo non trouvée.', 'Fehler, nicht unterstützte Video-URL oder das Video nicht gefunden.', 'Ошибка, не поддерживается видеоролик или видео не найдено.', 'Hata, desteklenmeyen video URLsi veya video bulunamadı.'),
(260, 'wrong_image_url', 'Wrong image url', 'عنوان ورل خاطئ للصورة', 'URL d&#039;image erronée', 'Falsche Bild-URL', 'Неверный URL изображения', 'Yanlış resim URLsi'),
(261, 'error_found_while_uploading', 'Error while uploading your image, please make sure you&#039;re uploading (gif,jpg,png) formats, also make sure the image is less than 3MB.', 'حدث خطأ أثناء تحميل الصورة، يرجى التأكد من تحميل تنسيقات (جيف و جبغ و ينغ)، وتأكد أيضا من أن الصورة أقل من 3 ميغابايت.', 'Une erreur lors du téléchargement de votre image, assurez-vous que vous téléchargez (gif, jpg, png) les formats, assurez-vous également que l&#039;image est inférieure à 3 Mo.', 'Fehler beim Hochladen Ihres Bildes, bitte stellen Sie sicher, dass Sie hochladen (gif, jpg, png) Formate, stellen Sie auch sicher, dass das Bild weniger als 3MB ist.', 'Ошибка при загрузке изображения, убедитесь, что вы загружаете (gif, jpg, png) форматы, также убедитесь, что изображение меньше 3 МБ.', 'Resminiz yüklenirken hata oluştu, lütfen (gif, jpg, png) biçimlerini yüklediğinizden emin olun; ayrıca resmin 3MBdan daha küçük olduğundan emin olun.'),
(262, 'wrong_tweet_url', 'Wrong tweet url', 'ورل تغريدة ورل خاطئ', 'URL de tweet erronée', 'Wrong tweet url', 'Неверный URL-адрес твита', 'Tweet urlsi yanlış'),
(263, 'wrror_getting_tweet', 'Error getting the tweet', 'حدث خطأ أثناء الحصول على سقسقة', 'Erreur lors de l&#039;obtention du tweet', 'Fehler beim Erhalten des Tweets', 'Ошибка при получении чириканья', 'Cıvıldama alınırken hata oluştu'),
(264, 'wrong_ig_url', 'Wrong instagram url', 'عنوان ورل ل إنستاغرام خاطئ', 'Url d&#039;instagram erronée', 'Falsche Instagram-URL', 'Неверный адрес instagram', 'Yanlış instagram urlsi'),
(265, 'error_getting_ig', 'Error getting the instagram post', 'حدث خطأ أثناء الحصول على مشاركة إنستاغرام', 'Erreur lors de la publication de l&#039;instagram', 'Fehler beim Hinzufügen des Instagrampostens', 'Ошибка при получении сообщения instagram', 'Instagram postası alınırken hata oluştu'),
(266, 'error_getting_post', 'Error getting the post', 'حدث خطأ أثناء الحصول على المشاركة', 'Erreur lors de la publication', 'Fehler beim Pfosten', 'Ошибка при получении сообщения', 'Mesaj alınırken hata oluştu'),
(267, 'wrong_fb_url', 'Wrong facebook post url', 'خطأ الفيسبوك آخر رابط', 'Wrong Facebook url post', 'Falsche Facebook-Post-URL', 'Неправильный URL-адрес', 'Yanlış facebook post urlsi'),
(268, 'wrong_soundcloud_url', 'Wrong soundcloud url', 'عنوان ورل سوندكلود خاطئ', 'Erreur erronée url', 'Falsche Soundcloud-URL', 'Неверный URL-адрес soundcloud', 'Yanlış sesli arama urlsi'),
(269, 'error_getting_sound', 'Error getting the sound', 'حدث خطأ أثناء الحصول على الصوت', 'Erreur pour obtenir le son', 'Fehler beim Klang', 'Ошибка при получении звука', 'Ses çıkarken hata oluştu'),
(270, 'current_password_dont_match', 'Current password doesn&#039;t match.', 'كلمة المرور الحالية غير متطابقة.', 'Le mot de passe actuel ne correspond pas.', 'Aktuelles Passwort stimmt nicht überein.', 'Текущий пароль не совпадает.', 'Geçerli şifre uyuşmuyor.'),
(271, 'new_password_dont_match', 'New password doesn&#039;t match.', 'كلمة المرور الجديدة غير متطابقة.', 'Le nouveau mot de passe ne correspond pas.', 'Neues Passwort stimmt nicht überein.', 'Новый пароль не соответствует.', 'Yeni şifre uyuşmuyor.'),
(272, 'your_account_was_deleted', 'Your account was deleted', 'تم حذف حسابك', 'Votre compte a été supprimé', 'Ihr Konto wurde gelöscht', 'Ваша учетная запись была удалена', 'Hesabınız silindi'),
(273, 'error_found_while_loading', 'Error found while loading', 'حدث خطأ أثناء التحميل', 'Erreur trouvée lors du chargement', 'Fehler beim Laden gefunden', 'Ошибка при загрузке', 'Yüklenirken hata bulundu'),
(274, 'text_content_is_required', 'Text content is required', 'محتوى النص مطلوب', 'Le contenu du texte est requis', 'Textinhalt ist erforderlich', 'Требуется текстовое содержимое', 'Metin içeriği gerekiyor'),
(275, 'text_content_must_be_more_than_50', 'Text content must be more than 50 letters', 'يجب أن يكون محتوى النص أكثر من 50 حرفا', 'Le contenu du texte doit comporter plus de 50 lettres', 'Textinhalt muss mehr als 50 Buchstaben sein', 'Текстовое содержимое должно содержать более 50 букв', 'Metin içeriği 50 harften fazla olmalıdır'),
(276, 'video_link_is_required', 'Video link is required', 'رابط الفيديو مطلوب', 'Un lien vidéo est requis', 'Video-Link ist erforderlich', 'Требуется ссылка на видео', 'Video bağlantısı gerekiyor'),
(277, 'video_type_is_required', 'Video type is required', 'نوع الفيديو مطلوب', 'Type de vidéo requis', 'Video-Typ ist erforderlich', 'Требуется тип видео', 'Video türü gerekiyor'),
(278, 'video_url_is_required', 'Video url is required', 'عنوان ورل للفيديو مطلوب', 'Une urne vidéo est requise', 'Video-URL ist erforderlich', 'Требуется URL-адрес видео', 'Video urlsi gerekiyor'),
(279, 'image_is_required', 'Image is required', 'الصورة مطلوبة', 'L&#039;image est requise', 'Bild ist erforderlich', 'Требуется изображение', 'Resim zorunludur'),
(280, 'invalid_image_url', 'Invalid Image url', 'عنوان ورل للصورة غير صالح', 'URL d&#039;image invalide', 'Ungültige Bild-URL', 'Недопустимый URL-адрес изображения', 'Geçersiz Resim URLsi'),
(281, 'tweet_url_is_required', 'Tweet url is required', 'مطلوب رابط ورل', 'Url Tweet requise', 'Tweet-URL ist erforderlich', 'Требуется URL-адрес url', 'Tweet url gerekiyor'),
(282, 'post_url_is_required', 'Post url is required', 'مطلوب رابط ورل', 'L&#039;URL de la poste est requise', 'Post-URL ist erforderlich', 'Требуется почтовый URL-адрес', 'Posta URLsi gerekiyor'),
(283, 'invalid_fb_url', 'Invalid facebook url', 'عنوان ورل لفيسبوك غير صالح', 'URL non identifiée de Facebook', 'Ungültige facebook url', 'Недопустимый URL-адрес facebook', 'Geçersiz facebook urlsi'),
(284, 'invalid_ig_url', 'Invalid instagram url', 'رابط إنستاغرام غير صالح', 'URL d&#039;instagram invalide', 'Ungültige Instagram-URL', 'Недопустимый url instagram', 'Geçersiz instagram urlsi'),
(285, 'soundcloud_is_required', 'Soundcloud url is required', 'مطلوب سوندكلود رابط', 'L&#039;urne Soundcloud est requise', 'Soundcloud-URL ist erforderlich', 'Требуется URL-адрес Soundcloud', 'Sesli arama urlsi gerekiyor'),
(286, 'wrong_sound_cloud_id', 'Wrong Soundcloud id', 'معرف سوندكلود خاطئ', 'ID de Soundcloud erroné', 'Wrong Soundcloud id', 'Неверный идентификатор Soundcloud', 'Yanlış Soundcloud kimliği'),
(287, 'option_title_is_required', 'Option title is required.', 'مطلوب عنوان الخيار.', 'Le titre de l&#039;option est requis.', 'Optionstitel ist erforderlich.', 'Требуется название опции.', 'Opsiyon başlığı gereklidir.'),
(288, 'is_empty', 'is empty', 'فارغ', 'est vide', 'ist leer', 'пусто', 'boş'),
(289, 'comments', 'Comments', 'تعليقات', 'Commentaires', 'Bemerkungen', 'Комментарии', 'Yorumlar'),
(290, 'write_comment', 'Write a comment', 'أكتب تعليقا', 'Écrire un commentaire', 'Schreibe einen Kommentar', 'Написать комментарий', 'Bir yorum Yaz'),
(291, 'reply', 'REPLY', 'الرد', 'Répondre', 'Antworten', 'Ответить', 'cevap'),
(292, 'write_comment_reply', 'Write a comment and press enter', 'اكتب تعليق واضغط على إنتر', 'Ecrivez un commentaire et appuyez sur Entrée', 'Schreiben Sie einen Kommentar und drücken Sie Enter', 'Напишите комментарий и нажмите enter.', 'Yorum yazın ve enter tuşuna basın'),
(293, 'load_more', 'Load more', 'تحميل المزيد', 'Charger plus', 'Laden Sie mehr', 'Загрузить больше', 'Daha fazla yükle'),
(294, 'no_more_comments', 'No more comments', 'لا المزيد من التعليقات', 'Plus de commentaires', 'Keine weiteren Kommentare', 'Больше комментариев нет', 'Başka yorum yok'),
(295, 'post', 'Post', 'نشر', 'Post', 'Post', 'Отправить', 'Post'),
(296, 'your_reaction', 'Your reaction?', 'ردة فعلك؟', 'Votre réaction?', 'Deine Reaktion?', 'Твоя реакция?', 'Tepkiniz?'),
(297, 'verification', 'Verification', 'التحقق', 'Vérification', 'Überprüfung', 'верификация', 'Doğrulama'),
(298, 'not_verified', 'Not verified', 'لم يتم التحقق منه', 'Non vérifié', 'Nicht verifiziert', 'не верифицирован', 'Doğrulanmadı'),
(299, 'verified', 'Verified', 'التحقق', 'Vérifié', 'Verifiziert', 'верифицирован', 'Doğrulanmış'),
(300, 'quizzes', 'Quizzes', 'مسابقات', 'Quiz', 'Quizzes', 'Викторины', 'Sınavlar'),
(301, 'latest_quizzes', 'Latest Quizzes', 'آخر مسابقات', 'Derniers tests', 'Neueste Quizzes', 'Последние опросы', 'Son Küçük Sınavlar'),
(302, 'quiz', 'Quiz', 'لغز', 'Quiz', 'Quiz', 'викторина', 'bilgi yarışması'),
(303, 'quiz_result', 'Quiz Result', 'نتيجة الاختبار', 'Résultat Résultat', 'Quiz Ergebnis', 'Результат викторины', 'Quiz Sonucu'),
(304, 'result', 'Result', 'نتيجة', 'Résultat', 'Ergebnis', 'результат', 'Sonuç'),
(305, 'add_desc', 'Add Description', 'اضف وصفا', 'Ajouter une description', 'Beschreibung hinzufügen', 'Добавить описание', 'Açıklama Ekle'),
(306, 'add_result', 'Add Result', 'إضافة نتيجة', 'Ajouter un résultat', 'Ergebnis hinzufügen', 'Добавить результат', 'Sonucu Ekle'),
(307, 'quiz_questions', 'Quiz questions', 'أسئلة مسابقة', 'Questions de quiz', 'Quiz Fragen', 'Вопросы викторины', 'Quiz sorular'),
(308, 'add_question', 'Add Question', 'إضافة سؤال', 'Ajouter une question', 'Frage hinzufügen', 'Добавить вопрос', 'Soru Ekle'),
(309, 'error', 'Error!', 'خطأ!', 'Erreur!', 'Fehler!', 'Ошибка!', 'Hata!'),
(310, 'min_results_2', 'Minimum of results must be 2!', 'يجب أن يكون الحد الأدنى من النتائج 2!', 'Le minimum de résultats doit être 2!', 'Minimum der Ergebnisse muss 2 sein!', 'Минимальный результат должен быть равен 2!', 'Minimum sonuç 2 olmalıdır!'),
(311, 'question_required', 'At least one question is required!', 'مطلوب سؤال واحد على الأقل!', 'Au moins une question est requise!', 'Mindestens eine Frage ist erforderlich!', 'Требуется хотя бы один вопрос!', 'En az bir soru gerekiyor!'),
(312, 'you_got', 'You got@:', 'أنت حصلت @:', 'Vous avez@:', 'Du hast@:', 'Ты получил@:', 'Sende var@:'),
(313, 'more_quizzes', 'More Quizzes', 'أكثر مسابقات', 'Plus de tests', 'Mehr Quizzes', 'Дополнительные опросы', 'Daha Fazla Kısa Sınavlar');
INSERT INTO `fl_langs` (`id`, `lang_key`, `english`, `arabic`, `french`, `germen`, `russian`, `turkish`) VALUES
(314, 'answers_not_modifiable', 'Created answers are not modifiable, you can still add new answers.', 'الإجابات التي تم إنشاؤها ليست قابلة للتعديل، لا يزال بإمكانك إضافة إجابات جديدة.', 'Les réponses créées ne sont pas modifiables, vous pouvez toujours ajouter de nouvelles réponses.', 'Erstellt Antworten sind nicht modifizierbar, können Sie noch neue Antworten hinzufügen.', 'Созданные ответы не изменяются, вы можете добавить новые ответы.', 'Oluşturulan cevaplar değiştirilebilir değildir, yine de yeni cevaplar ekleyebilirsiniz.'),
(315, 'file_is_big', 'The file is too big, please increase your server upload limit in php.ini', 'الملف كبير جدا، يرجى زيادة حد تحميل الخادم في php.ini', 'Le fichier est trop grand, augmentez la limite de téléchargement de votre serveur dans php.ini', 'Die Datei ist zu groß, bitte erhöhen Sie Ihre Server-Upload-Limit in php.ini', 'Файл слишком большой, пожалуйста, увеличьте лимит загрузки сервера в php.ini', 'Dosya çok büyük, lütfen php.ini dosyasındaki sunucu yükleme sınırını arttır'),
(316, 'enter_valid_name', 'Please enter a valid name!', 'رجاء ادخل اسما صحيحا!', 'Merci d&#039;entrer un nom valide!', 'Bitte geben Sie einen gültigen Namen ein!', 'Пожалуйста, введите верное имя!', 'Lütfen geçerli bir isim girin!'),
(317, 'invalid_last_name', 'Last name is too long!', 'اسم العائلة طويل جدا!', 'Le nom de famille est trop long!', 'Nachname ist zu lang!', 'Фамилия слишком длинная!', 'Soyadı çok uzun!'),
(318, 'id_file_invalid', 'The Passport/ID file is Not valid.please select a valid picture!', 'ملف باسبورت / إد غير صالح. يرجى تحديد صورة صالحة!', 'Le fichier Passport / ID n&#039;est pas valide. Veuillez sélectionner une image valide!', 'Die Pass / ID-Datei ist nicht gültig.Bitte wählen Sie ein gültiges Bild!', 'Файл Passport / ID не действителен. Выберите правильное изображение!', 'Passport / Kimlik dosyası geçersiz Geçerli bir resim seçin!'),
(319, 'img_file_invalid', 'The Image file is Not valid.please select a valid picture!', 'ملف الصورة غير صالح.الرجاء تحديد صورة صالحة!', 'Le fichier image n&#039;est pas valide. Veuillez sélectionner une image valide!', 'Die Bilddatei ist nicht gültig.Bitte wählen Sie ein gültiges Bild!', 'Файл изображения недействителен. Выберите правильное изображение!', 'Resim dosyası geçerli değil. Geçerli bir resim seçin!'),
(320, 'id_file_mustbe_img', 'The passport/ID picture must be an image', 'يجب أن تكون صورة الجواز / الهوية صورة', 'La photo passeport / ID doit être une image', 'Der Pass / ID-Bild muss ein Bild sein', 'Паспорт / удостоверение личности должно быть изображением', 'Pasaport / kimlik resmi bir resim olmalıdır.'),
(321, 'user_file_mustbe_img', 'The user profile picture must be an image.', 'يجب أن تكون صورة الملف الشخصي للمستخدم صورة.', 'L&#039;image de profil utilisateur doit être une image.', 'Das Benutzerprofilbild muss ein Bild sein.', 'Изображение профиля пользователя должно быть изображением.', 'Kullanıcı profili resmi bir resim olmalıdır.'),
(322, 'verif_request_sent', 'Your request was successfully sent and will be in the near future reviwed!', 'تم إرسال طلبك بنجاح وسوف تكون في المستقبل القريب ريفيويد!', 'Votre demande a été envoyée avec succès et sera bientôt reprise!', 'Ihre Anfrage wurde erfolgreich gesendet und wird in naher Zukunft umgeschlagen!', 'Ваш запрос был успешно отправлен и будет в ближайшее время рассмотрен!', 'Talebiniz başarıyla gönderildi ve yakın gelecekte yeniden incelenecek!'),
(323, 'invalid_verif_request', 'Error while sending your request, please make sure you&#039;re uploading (gif,jpg,png) formats, also make sure the image is less than 3MB.', 'حدث خطأ أثناء إرسال طلبك، يرجى التأكد من إعادة تحميل تنسيقات (جيف، جبغ، ينغ)، وتأكد أيضا من أن الصورة أقل من 3 ميغابايت.', 'Erreur lors de l&#039;envoi de votre demande, assurez-vous que vous êtes en train de télécharger des formats (gif, jpg, png), assurez-vous également que l&#039;image est inférieure à 3 Mo.', 'Fehler beim Senden Ihrer Anfrage, bitte stellen Sie sicher, dass Sie hochladen (gif, jpg, png) Formate, stellen Sie auch sicher, dass das Bild weniger als 3MB ist.', 'Ошибка при отправке вашего запроса, убедитесь, что вы загружаете (gif, jpg, png) форматы, также убедитесь, что изображение меньше 3 МБ.', 'İsteğiniz gönderilirken hata oluştu, lütfen (gif, jpg, png) biçimlerini yeniden yüklediğinizden emin olun ve ayrıca resmin 3MB&#039;dan küçük olduğundan emin olun.'),
(324, 'verif_request_pending', 'Your request was received and is pending approval!', 'تم استلام طلبك وهو في انتظار الموافقة!', 'Votre demande a été reçue et est en attente d&#039;approbation!', 'Ihre Anfrage wurde eingegangen und steht noch aus.', 'Ваш запрос был получен и находится на рассмотрении!', 'Talebiniz alındı ve onay bekliyor!'),
(325, 'verif_request_accepted', 'Congratulations, you&#039;re verified. Thanks for verifying your ID', 'تهانينا، لقد تم إثبات ملكيتك. نشكرك على إثبات هويتك', 'Félicitations, vous êtes vérifié. Merci d&#039;avoir vérifié votre identifiant', 'Herzlichen Glückwunsch, du bist verifiziert Vielen Dank für die Überprüfung Ihrer ID', 'Поздравляем, вы подтверждены. Спасибо, что подтвердили свой идентификатор', 'Tebrikler, doğrulanmışsın. Kimliğinizi doğruladığınız için teşekkür ederiz'),
(326, 'upload_id', 'Upload Passport or ID', 'تحميل جواز السفر أو الهوية', 'Télécharger un passeport ou une pièce d&#039;identité', 'Upload Pass oder ID', 'Загрузить паспорт или идентификатор', 'Pasaport veya Kimliği Yükle'),
(327, 'please_upload_passport_id', 'Please upload a picture of your passport / ID so we can consider your request', 'يرجى تحميل صورة لجواز السفر / الهوية الخاصة بك حتى نتمكن من النظر في طلبك', 'Veuillez télécharger une image de votre passeport / ID afin que nous puissions considérer votre demande', 'Bitte laden Sie ein Bild von Ihrem Pass / ID, damit wir Ihre Anfrage berücksichtigen können', 'Загрузите изображение своего паспорта / удостоверения личности, чтобы мы могли рассмотреть ваш запрос', 'İsteğinizi düşünebilmemiz için lütfen pasaportunuzun / kimliğinizin resmini yükleyin'),
(328, 'verify_data_fields', 'make sure that data fields and photo are visible', 'تأكد من أن حقول البيانات والصورة مرئية', 'assurez-vous que les champs de données et la photo sont visibles', 'Stellen Sie sicher, dass Datenfelder und Fotos sichtbar sind', 'убедитесь, что поля данных и фотография видны', 'veri alanlarının ve fotoğrafın görünür olduğundan emin olun'),
(329, 'your_photo', 'your distinct photo', 'صورتك المميزة', 'votre photo distincte', 'Ihr eigenes Foto', 'твоя отличная фотография', 'farklı fotoğrafın'),
(330, 'send', 'Send', 'إرسال', 'Envoyer', 'Senden', 'послать', 'Mesaj'),
(331, 'message', 'Message', 'رسالة', 'Message', 'Nachricht', 'Сообщение', NULL),
(332, 'br_news', 'Breaking News', 'أخبار عاجلة', 'dernières nouvelles', 'Aktuelle Nachrichten', 'Последние новости', 'Son Dakika Haberleri'),
(333, 'read_more', 'Read more', 'اقرأ أكثر', 'Lire la suite', 'Weiterlesen', 'Прочитайте больше', 'Daha fazla oku'),
(334, 'your_name', 'Your name', 'اسمك', 'Votre nom', 'Dein Name', 'Ваше имя', 'Adınız'),
(335, 'recipients_email', 'Recipient Email', 'البريد الإلكتروني للمستلم', 'Courrier électronique du destinataire', 'Empfänger E-Mail', 'Электронный адрес получателя', 'Alıcının E-postası'),
(336, 'name', 'Name', 'اسم', 'prénom', 'Name', 'имя', 'isim'),
(337, 'write_message', 'Write a message', 'اكتب رسالة', 'Écrire un message', 'Eine Nachricht schreiben', 'Напиши сообщение', 'Bir mesaj yaz'),
(338, 'br_news_saved', 'Breaking news  was successful saved!', 'تم حفظ الأخبار العاجلة بنجاح!', 'Les dernières nouvelles ont été enregistrées avec succès!', 'Breaking news war erfolgreich gerettet!', 'Последние новости были успешно сохранены!', 'Kesilen haberler başarıyla kaydedildi!'),
(339, 'invalid_time', 'Time limit is wrong.Please enter a valid number', 'المهلة غير صحيحة. الرجاء إدخال رقم صحيح', 'Le délai est incorrect. Veuillez entrer un numéro valide', 'Die Frist ist falsch. Bitte geben Sie eine gültige Nummer ein', 'Ограничение по времени неверно. Введите действительное число', 'Zaman sınırı yanlış.Lütfen geçerli bir sayı girin'),
(340, 'invalid_news_url', 'News link is wrong.Please enter a valid URL!', 'رابط الأخبار خاطئ. الرجاء إدخال عنوان ورل صالح!', 'Le lien de nouvelles est incorrect. Veuillez entrer une URL valide!', 'News Link ist falsch.Bitte geben Sie eine gültige URL!', 'Ссылка на новости неверна. Введите действительный URL!', 'Haber bağlantısı yanlış.Lütfen geçerli bir URL girin!'),
(341, 'br_news_added', 'Breaking news  was successful added!', 'تمت إضافة الأخبار العاجلة بنجاح!', 'Les dernières nouvelles ont été ajoutées avec succès!', 'Breaking news war erfolgreich hinzugefügt!', 'Последние новости были успешно добавлены!', 'Son dakika haberi eklendi!'),
(342, 'design', 'Design', 'التصميم', 'Conception', 'Entwurf', 'дизайн', 'dizayn'),
(343, 'header', 'Header', 'رأس', 'Entête', 'Header', 'заголовок', 'Başlık'),
(344, 'logo_size', 'Logo (290x120)', 'شعار (290 × 120)', 'Logo (290x120)', 'Logo (290x120)', 'Логотип (290x120)', 'Logo (290x120)'),
(345, 'favicon', 'Favicon', 'فافيكون', 'Favicon', 'Favicon', 'Favicon', 'favicon'),
(346, 'themes', 'Themes', 'المواضيع', 'Thèmes', 'Themen', 'Темы', 'Temalar'),
(347, 'version', 'Version', 'الإصدار', 'Version', 'Version', 'Версия', 'versiyon'),
(348, 'author', 'Author', 'مؤلف', 'Auteur', 'Autor', 'автор', 'Yazar'),
(349, 'announcement', 'Announcement', 'إعلان', 'Annonce', 'Ankündigung', 'Объявление', 'duyuru'),
(350, 'active_anno', 'Active announcements', 'الإعلانات النشطة', 'Annonces actives', 'Aktive Ankündigungen', 'Активные объявления', 'Aktif duyurular'),
(351, 'inactive_anno', 'Inactive announcements', 'الإعلانات غير النشطة', 'Annonces inactives', 'Inaktive Ansagen', 'Неактивные объявления', 'Etkin olmayan duyurular'),
(352, 'add', 'Add', 'إضافة', 'Ajouter', 'Hinzufügen', 'Добавить', 'Eklemek'),
(353, 'expiration_time', 'Expiration Time', 'وقت انتهاء الصلاحية', 'Date d&#039;expiration', 'Ablaufzeit', 'Время истечения', 'Sona Erme Süresi'),
(354, 'link', 'Link', 'حلقة الوصل', 'Lien', 'Verknüpfung', 'Ссылка', 'bağlantı'),
(355, 'source_addr', 'Source address', 'عنوان المصدر', 'Adresse source', 'Quelladresse', 'Адрес источника', 'Kaynak adresi'),
(356, 'add_br_news', 'Add breaking news item', 'إضافة خبر عاجل', 'Ajouter un article récapitulatif', 'Füge brechende Nachricht hinzu', 'Добавить рубрику новостей', 'Son dakika haber ekle'),
(357, 'publish_immediately', 'Publish Immediately', 'نشر فورا', 'Publier immédiatement', 'Unmittelbar veröffentlichen', 'Опубликовать сразу', 'Derhal Yayınla'),
(358, 'br_news_time', 'Time limit to show breaking news', 'المهلة لعرض الأخبار العاجلة', 'Délai pour afficher les dernières nouvelles', 'Zeitlimit, um aktuelle Nachrichten zu zeigen', 'Ограничение времени, чтобы показать последние новости', 'Son dakika haberler için zaman sınırı'),
(359, 'edit_br_news', 'Edit breaking news item', 'تعديل خبر عاجل', 'Modifier l&#039;actualité', 'Bearbeiten brechende Nachricht', 'Редактировать новости', 'Yeni haber öğesini düzenle'),
(360, 'rss_source', 'RSS Feed Source', 'مصدر خلاصة رسس', 'Source de flux RSS', 'RSS-Feed Quelle', 'Источник RSS-ленты', 'RSS Kaynağı Kaynağı'),
(361, 'rss_limit', 'RSS Items Limit', 'رسس عناصر الحد', 'Limites des éléments RSS', 'RSS Items Limit', 'RSS Предельные значения', 'RSS Öğeleri Limit'),
(362, 'enable', 'Enable', 'مكن', 'Activer', 'Aktivieren', 'включить', 'etkinleştirme'),
(363, 'disable', 'Disable', 'تعطيل', 'Désactiver', 'Deaktivieren', 'запрещать', 'Devre dışı'),
(364, 'hide', 'Hide', 'إخفاء', 'Cacher', 'Verstecken', 'Спрятать', 'Saklamak'),
(365, 'upload', 'Upload', 'تحميل', 'Télécharger', 'Hochladen', 'Загрузить', 'Yükleme'),
(366, 'email_sent', 'Your email was sent successfully!', 'تم إرسال البريد الإلكتروني الخاص بك بنجاح!', 'Votre E-mail a été envoyé avec succès!', 'Deine E-Mail wurde erfolgreich gesendet!', 'Ваше письмо успешно отправлено!', 'E-postanız başarıyla gönderildi!'),
(367, 'func_not_available', 'Sorry, this function is temporarily not available', 'عذرا، هذه الوظيفة غير متوفرة مؤقتا', 'Désolé, cette fonction n&#039;est temporairement pas disponible', 'Diese Funktion ist vorübergehend nicht verfügbar', 'К сожалению, эта функция временно недоступна', 'Maalesef, bu işlev geçici olarak kullanılamıyor'),
(368, 'report_post', 'Report Post!', 'الإبلاغ عن المشاركة!', 'Signaler un commentaire!', 'Beitrag melden!', 'Сообщить модератору', 'Yazıyı Gönder!'),
(369, 'report_received', 'Your report has been successfully received!', 'لقد تم استلام تقريرك بنجاح!', 'Votre rapport a été reçu avec succès!', 'Ihr Bericht wurde erfolgreich empfangen!', 'Ваш отчет успешно принят!', 'Raporunuz başarıyla alındı!'),
(370, 'report_canceled', 'Your report has been canceled!', 'تم إلغاء تقريرك!', 'Votre rapport a été annulé!', 'Ihr Bericht wurde storniert!', 'Ваш отчет отменен!', 'Raporunuz iptal edildi!'),
(371, 'cancel_report', 'Cancel Report!', 'إلغاء التقرير!', 'Annuler le rapport!', 'Bericht abbrechen!', 'Отменить отчет!', 'Raporu İptal Et!'),
(372, 'more_info', 'More info', 'مزيد من المعلومات', 'Plus d&#039;informations', 'Mehr Info', 'Больше информации', 'Daha fazla bilgi'),
(373, 'max_upload_size_is', 'Error: File is too big, Max upload size is:', 'خطأ: الملف كبير جدا، الحد الأقصى لحجم التحميل هو:', 'Erreur: le fichier est trop grand, la taille de téléchargement maximale est:', 'Fehler: Datei ist zu groß, Maximale Uploadgröße ist:', 'Ошибка: файл слишком большой, максимальный размер загрузки:', 'Hata: Dosya çok büyük, maksimum yükleme boyutu:'),
(374, 'language', 'Language', 'اللغة', 'Langue', 'Sprache', 'язык', 'Dil'),
(375, 'go_pro', 'Go pro', 'الذهاب للمحترفين', 'Go Pro', 'Geh pro', 'Go pro', 'Yanlışa git'),
(376, 'pro_packages', 'Pro active packages', 'برو حزم النشطة', 'Forfaits pro-actifs', 'Pro aktive Pakete', 'Проактивные пакеты', 'Pro aktif paketler'),
(377, 'discover_features', 'Discover More Features With ( {{SITE_NAME}} ) Pro Package!', 'اكتشاف المزيد من الميزات مع ({{SITE_NAME}}) حزمة برو!', 'Découvrez plus de fonctionnalités avec ({{SITE_NAME}}) Pro Package!', 'Entdecken Sie weitere Funktionen mit ({{SITE_NAME}}) Pro-Paket!', 'Откройте для себя дополнительные возможности с ({{SITE_NAME}}) Pro Package!', '({{SITE_NAME}}) ile Daha Fazla Özellikler Keşfedin Pro Paketi!'),
(378, 'free_mbr', 'Free member', 'عضو مجاني', 'Membre gratuit', 'Freies Mitglied', 'Бесплатный член', 'Ücretsiz Üye'),
(379, 'pro_mbr', 'Pro member', 'عضو برو', 'Membre pro', 'Pro Mitglied', 'Про член', 'Pro üyesi'),
(380, 'pkg_posting_limit', 'Posting limit {{LIMIT}} articles', 'حد النشر {{ليميت}} من المقالات', 'Limite de publication {{LIMIT}} articles', 'Beitragslimit {{LIMIT}} Artikel', 'Предел проводки {{LIMIT}}', 'Yayınlanma sınırı {{LIMIT}} makale'),
(381, 'not_featured_posts', 'Not featured posts', 'ليست مشاركات مميزة', 'Messages non affichés', 'Nicht gekennzeichnete Beiträge', 'Не указано', 'Özellikli yayın yok'),
(382, 'ads_show_up', 'Ads will show up', 'ستظهر الإعلانات', 'Les annonces apparaîtront', 'Anzeigen werden angezeigt', 'Объявления будут отображаться', 'Reklamlar gösterilir'),
(383, 'no_verified_badge', 'No verified badge', 'لم يتم التحقق من شارة', 'Aucun badge vérifié', 'Kein verifiziertes Abzeichen', 'Нет подтвержденного значка', 'Doğrulanmış rozet yok'),
(384, 'stay_free', 'Stay free', 'ابق حرا', 'Reste libre', 'Bleibe frei', 'Оставайся свободным', 'Özgür kal'),
(385, 'featured_posts', 'Featured posts', 'المشاركات مميزة', 'Postes en vedette', 'Beliebte Beiträge', 'Популярные сообщения', 'Öne çıkan gönderiler'),
(386, 'ads_wont_show_up', 'No ads will show up', 'لن تظهر أية إعلانات', 'Aucune annonce ne s&#039;affichera', 'Keine Werbung wird angezeigt', 'Объявления не будут отображаться', 'Hiçbir reklam gösterilmez'),
(387, 'verified_badge', 'Verified badge', 'تم التحقق من الشارة', 'Badge vérifié', 'Überprüftes Abzeichen', 'Проверенный значок', 'Doğrulanmış rozet'),
(388, 'upgrade', 'Upgrade', 'تطوير', 'Améliorer', 'Aktualisierung', 'Обновить', 'Yükselt'),
(389, 'u_are_pro', 'You have successfully upgraded you profile to PRO user!', 'لقد نجحت في ترقية ملفك الشخصي إلى مستخدم برو!', 'Vous avez mis à jour votre profil avec succès vers l&#039;utilisateur PRO!', 'Sie haben Ihr Profil erfolgreich auf PRO-Benutzer aktualisiert!', 'Вы успешно обновили свой профиль до пользователя PRO!', 'Profilinizi PRO kullanıcılarına başarıyla yükselttiniz!'),
(390, 'start_new_features', 'Done, Start browsing new features', 'تم، ابدأ تصفح الميزات الجديدة', 'Terminé, commencez à parcourir les nouvelles fonctionnalités', 'Fertig, Starten Sie das Durchsuchen neuer Funktionen', 'Сделано, начните просмотр новых функций', 'Bitti, yeni özelliklere göz atmaya başlayın'),
(391, 'wallet', 'Wallet', 'محفظة نقود', 'Portefeuille', 'Brieftasche', 'Бумажник', 'Cüzdan'),
(392, 'ads', 'Advertising', 'إعلان', 'La publicité', 'Werbung', 'реклама', 'reklâm'),
(393, 'balance', 'Available balance', 'الرصيد المتوفر', 'Solde disponible', 'Verfügbares Guthaben', 'Доступные средства', 'Kalan bakiye'),
(394, 'add_money', 'Add money', 'إضافة المال', 'Ajouter de l&#039;argent', 'Geld hinzufügen', 'Добавить деньги', 'Para ekle'),
(395, 'enter_amount', 'Enter amount', 'أدخل المبلغ', 'Entrer le montant', 'Menge eingeben', 'Введите сумму', 'Miktarı girin'),
(396, 'replenish_balance', 'Replenish balance', 'تجديد الرصيد', 'Réapprovisionner l&#039;équilibre', 'Ausgleich auffüllen', 'Пополнить баланс', 'Dengeyi tekrar doldurun'),
(397, 'ur_balance_refilled', 'Your balance has been successfully refilled to {{AMOUNT}} USD', 'تمت إعادة تعبئة رصيدك بنجاح إلى {{أمونت}} دولار أمريكي', 'Votre solde a bien été rempli à {{AMOUNT}} USD', 'Ihr Guthaben wurde erfolgreich auf {{AMOUNT}} USD aufgefüllt', 'Ваш баланс был успешно добавлен в {{AMOUNT}} USD', 'Bakiyeniz {{AMOUNT}} USD&#039;ye başarıyla tekrar dolduruldu.'),
(398, 'create_ad', 'Create ad', 'أعلن', 'Créer une publicité', 'Anzeige erstellen', 'Создать объявление', 'Reklam oluştur'),
(399, 'cname', 'Company name', 'اسم الشركة', 'Nom de la compagnie', 'Name der Firma', 'Название компании', 'Şirket Adı'),
(400, 'results', 'Results', 'النتائج', 'Résultats', 'Ergebnisse', 'Результаты', 'Sonuçlar'),
(401, 'spent', 'Spent', 'أنفق', 'Dépensé', 'Verbraucht', 'потраченный', 'harcanmış'),
(402, 'published', 'Published', 'نشرت', 'Publié', 'Veröffentlicht', 'опубликованный', 'Yayınlanan'),
(403, 'dest_url', 'Destination URL', 'عنوان ورل المقصود', 'URL de destination', 'Ziel-URL', 'Целевой URL', 'hedef URL'),
(404, 'placement', 'Placement', 'تحديد مستوى', 'Placement', 'Platzierung', 'размещение', 'Yerleştirme'),
(405, 'sidebar', 'Sidebar', 'الشريط الجانبي', 'Barre latérale', 'Seitenleiste', 'Боковая панель', 'Kenar çubuğu'),
(406, 'hpage', 'Home page', 'الصفحة الرئيسية', 'Page d&#039;accueil', 'Startseite', 'Главная страница', 'Ana sayfa'),
(407, 'spage', 'Story page', 'صفحة القصة', 'Page d&#039;histoire', 'Geschichte Seite', 'История', 'Öykü sayfası'),
(408, 'choose_image', 'Choose image', 'اختر صورة', 'Choisir une image', 'Wähle ein Bild', 'Выберите изображение', 'Resmi seçin'),
(409, 'no_file_chosen', 'no file chosen', 'لم تقم باختيار ملف', 'aucun fichier choisi', 'keine Datei ausgewählt', 'файл не выбран', 'dosya seçili değil'),
(410, 'submit', 'Submit', 'خضع', 'Soumettre', 'einreichen', 'Отправить', 'Gönder'),
(411, 'invalid_url', 'Invalid URL. Please enter a valid URL!', 'URL غير صالح. أدخل رابط صحيح من فضلك!', 'URL invalide. S&#039;il vous plaît entrer une URL valide!', 'Ungültige URL. Bitte geben Sie eine gültige URL ein!', 'Неверная ссылка. Пожалуйста, введите корректный адрес!', 'Geçersiz URL. Lütfen geçerli bir adres girin!'),
(412, 'ad_created', 'Your ad has been created successfully', 'تم إنشاء إعلانك بنجاح', 'Votre annonce a été créée avec succès', 'Ihre Anzeige wurde erfolgreich erstellt', 'Ваше объявление было успешно создано', 'Reklamınız başarıyla oluşturuldu'),
(413, 'ad_saved', 'Your changes to the ad were successfully saved', 'تم حفظ التغييرات التي أجريتها على الإعلان بنجاح', 'Vos modifications de l&#039;annonce ont été enregistrées avec succès', 'Ihre Änderungen an der Anzeige wurden erfolgreich gespeichert', 'Ваши изменения в объявлении были успешно сохранены', 'Reklamdaki değişiklikler başarıyla kaydedildi'),
(414, 'promoted_by', 'Promoted by', 'روجت', 'Promu par', 'Befördert von', 'Продвижение', 'Tarafından teşvik'),
(415, 'refill_balance', 'Your current wallet balance is: {{AMOUNT}}, please top up your wallet to continue.', 'رصيد المحفظة الحالي هو: {{أمونت}}، يرجى متابعة محفظتك للمتابعة.', 'Votre solde de portefeuille actuel est: {{AMOUNT}}, veuillez recharger votre porte-monnaie pour continuer.', 'Ihr aktuelles Google Wallet-Guthaben beträgt: {{AMOUNT}}. Bitte füllen Sie Ihren Geldbeutel auf, um fortzufahren.', 'Ваш текущий баланс кошелька: {{AMOUNT}}, пожалуйста, пополните свой кошелек, чтобы продолжить.', 'Mevcut cüzdan bakiyeniz: {{AMOUNT}}, devam etmek için lütfen cüzdanınızı doldurun.'),
(416, 'top_up', 'Top Up', 'فوق حتى', 'Recharger', 'Nachfüllen', 'Наверх', 'Top Up'),
(417, 'create_new_ad', 'Create new ad', 'إنشاء إعلان جديد', 'Créer une nouvelle annonce', 'Erstellen Sie eine neue Anzeige', 'Создать новое объявление', 'Yeni reklam oluştur'),
(418, 'view_more', 'View More', 'عرض المزيد', 'Afficher plus', 'Mehr sehen', 'Просмотреть больше', 'Daha fazla göster'),
(419, 'pick_plan', 'Pick your plan', 'اختر خطتك', 'Choisissez votre plan', 'Wählen Sie Ihren Plan', 'Выберите свой план', 'Planını seç'),
(420, 'posting_limit', 'Posting limit', 'حد النشر', 'Limite de publication', 'Buchungslimit', 'Предел проводки', 'Gönderme sınırı'),
(421, 'no_ads', 'No Ads', 'لا اعلانات', 'Pas de pubs', 'Keine Werbung', 'Без рекламы', 'Reklamsız'),
(422, 'delete', 'Delete', 'حذف', 'Effacer', 'Löschen', 'Удалить', 'silmek'),
(423, 'view_profile', 'View Profile', 'عرض الصفحة الشخصية', 'Voir le profil', 'Profil anzeigen', 'Просмотреть профиль', 'Profili Görüntüle'),
(424, 'pinterest', 'Pinterest', 'موقع Pinterest', 'Pinterest', 'Pinterest', 'Pinterest', 'pinterest'),
(425, 'reading', 'Reading', 'قراءة', 'En train de lire', 'lesen', 'чтение', 'Okuma'),
(426, 'are_you_sure_delete_account', 'Are you sure you want to delete your account? Your account will be permanetly removed!', 'هل انت متأكد انك تريد حذف حسابك؟ ستتم إزالة حسابك نهائيًا!', 'Êtes-vous sûr de vouloir supprimer votre compte? Votre compte sera définitivement supprimé!', 'Möchtest du dein Konto wirklich löschen? Dein Account wird dauerhaft entfernt!', 'Вы действительно хотите удалить свою учетную запись? Ваша учетная запись будет удалена навсегда!', 'Hesabınızı silmek istediğinizden emin misiniz? Hesabınız kalıcı olarak kaldırılacak!'),
(427, 'terms_agreement', 'By creating your account, you agree to our', 'عن طريق إنشاء حسابك ، فإنك توافق على', 'En créant votre compte, vous acceptez notre', 'Mit der Erstellung Ihres Benutzerkontos stimmen Sie unseren Nutzungsbedingungen zu', 'Создав свою учетную запись, вы соглашаетесь с нашими', 'Hesabınızı oluşturarak,'),
(428, 'images', 'Images', 'صور', 'Images', 'Bilder', 'Изображений', 'Görüntüler'),
(429, 'result_for', 'Results for', 'نتائج', 'résultats pour', 'Ergebnisse für', 'Результаты для', 'Için sonuçlar'),
(430, 'subscribe_us', 'Subscribe Us', 'اشترك معنا', 'Abonnez-vous', 'Abonnieren Sie uns', 'Подписаться на новости', 'Bize üye olun'),
(431, 'subscribe_now', 'Subscribe Now', 'إشترك الآن', 'Abonnez-vous maintenant', 'Abonniere jetzt', 'Подпишись сейчас', 'Şimdi Abone Ol'),
(432, 'subscribe_info', 'Subscribe to our email newsletter &amp; receive updates right in your inbox.', 'اشترك في النشرة الإخبارية عبر البريد الإلكتروني وتلقي التحديثات في بريدك الوارد مباشرة.', 'Abonnez-vous à notre newsletter et recevez des mises à jour directement dans votre boîte de réception.', 'Abonnieren Sie unseren E-Mail-Newsletter und erhalten Sie Updates direkt in Ihrem Posteingang.', 'Подпишитесь на нашу рассылку новостей и получайте обновления прямо в своем почтовом ящике.', 'E-posta bültenimize abone olun ve güncellemeleri doğrudan gelen kutunuzda alın.'),
(433, 'cookie_message', 'This website uses cookies to ensure you get the best experience on our website.', 'يستخدم موقع الويب هذا ملفات تعريف الارتباط لضمان حصولك على أفضل تجربة على موقعنا.', 'Ce site utilise des cookies pour vous assurer la meilleure expérience sur notre site.', 'Diese Website verwendet Cookies, um sicherzustellen, dass Sie die beste Erfahrung auf unserer Website erhalten.', 'На этом веб-сайте используются файлы cookie, чтобы вы могли получить лучший опыт на нашем веб-сайте.', 'Bu web sitesi, web sitemizde en iyi deneyimi yaşamanızı sağlamak için çerezleri kullanır.'),
(434, 'cookie_dismiss', 'Got It!', 'فهمتك!', 'Je l&#039;ai!', 'Ich habs!', 'Понял!', 'Anladım!'),
(435, 'cookie_link', 'Learn More', 'أعرف أكثر', 'Apprendre encore plus', 'Erfahren Sie mehr', 'Выучить больше', 'Daha fazla bilgi edin'),
(436, 'related', 'Related', 'ذات صلة', 'en relation', 'verbunden', 'Связанный', 'İlgili'),
(437, 'create', 'Create', 'خلق', 'Créer', 'Erstellen', 'Создайте', 'Oluşturmak'),
(438, 'trivia', 'Trivia', 'أمور تافهة', 'Trivia', 'Wissenswertes', 'Мелочи', 'Önemsiz şeyler'),
(439, 'correct', 'Correct', 'صيح', 'Correct', 'Richtig', 'Верный', 'Doğru'),
(440, 'reveal_when_answered', 'Reveal When Answered', 'كشف عند الرد', 'Révéler une fois répondu', 'Bei Beantwortung anzeigen', 'Показать, когда ответят', 'Cevaplandığında Ortaya Çıkar'),
(441, 'min_results_more_than', 'Minimum of results must be more than question count', 'يجب أن يكون الحد الأدنى من النتائج أكثر من عدد الأسئلة', 'Le minimum de résultats doit être supérieur au nombre de questions', 'Das Minimum an Ergebnissen muss mehr als die Anzahl der Fragen sein', 'Минимум результатов должен быть больше, чем количество вопросов', 'Minimum sonuç sayısı soru sayısından fazla olmalıdır'),
(442, 'moderator', 'Moderator', 'مشرف', 'Modérateur', 'Moderator', 'Модератор', 'Moderatör'),
(443, 'switch_account', 'Switch Account', 'تبديل الحساب', 'Changer de compte', 'Benutzer wechseln', 'Сменить аккаунт', 'Hesap değiştir'),
(444, 'accounts', 'Accounts', 'حسابات', 'Comptes', 'Konten', 'Счета', 'Hesaplar'),
(445, 'add_account', 'Add Account', 'إضافة حساب', 'Ajouter un compte', 'Konto hinzufügen', 'Добавить аккаунт', 'Hesap eklemek'),
(446, 'your_already_loggedin_account', 'You already loggedin to this account', 'لقد قمت بتسجيل الدخول بالفعل إلى هذا الحساب', 'Vous êtes déjà connecté à ce compte', 'Sie haben sich bereits in diesem Konto angemeldet', 'Вы уже вошли в эту учетную запись', 'Bu hesaba zaten giriş yaptınız'),
(447, 'choose_a_payment_method', 'Choose a payment method', 'اختر طريقة الدفع', 'Choisissez une méthode de paiement', 'Wählen Sie eine Bezahlungsart', 'Выберите способ оплаты', 'Bir ödeme yöntemi seçin'),
(448, 'stripe', 'Stripe', 'شريط', 'Bande', 'Streifen', 'Полоса', 'Şerit'),
(449, 'c_payment', 'Confirming payment, please wait..', 'جاري تأكيد الدفع ، يرجى الانتظار ..', 'Confirmation du paiement, veuillez patienter.', 'Bestätigung der Zahlung, bitte warten ..', 'Подтверждение оплаты, подождите ..', 'Ödeme onaylanıyor, lütfen bekleyin ..'),
(450, 'payment_declined', 'Payment declined, please try again later.', 'تم رفض الدفع ، يرجى المحاولة مرة أخرى في وقت لاحق.', 'Paiement refusé, veuillez réessayer plus tard.', 'Zahlung abgelehnt, bitte versuchen Sie es später erneut.', 'Платеж отклонен, повторите попытку позже.', 'Ödeme reddedildi, lütfen daha sonra tekrar deneyin.'),
(451, 'something_went_wrong', 'Something went wrong', 'هناك خطأ ما', 'Un problème est survenu', 'Etwas ist schief gelaufen', 'Что-то пошло не так', 'Bir şeyler yanlış gitti'),
(452, '2checkout', '2checkout', '2 الخروج', '2checkout', '2checkout', '2 касс', '2.Kasa'),
(453, 'address', 'Address', 'عنوان', 'Adresse', 'Adresse', 'Адрес', 'Adres'),
(454, 'city', 'City', 'مدينة', 'Ville', 'Stadt', 'город', 'Kent'),
(455, 'state', 'State', 'حالة', 'Etat', 'Zustand', 'состояние', 'Durum'),
(456, 'zip', 'ZIP', 'ZIP', 'ZIP *: FRANÇAIS', 'POSTLEITZAHL', 'ZIP', 'ZIP'),
(457, 'phone_number', 'Phone Number', 'رقم الهاتف', 'Numéro de téléphone', 'Telefonnummer', 'Телефонный номер', 'Telefon numarası'),
(458, 'card_number', 'Card Number', 'رقم البطاقة', 'Numéro de carte', 'Kartennummer', 'Номер карты', 'Kart numarası'),
(459, 'pay', 'Pay', 'دفع', 'Payer', 'Zahlen', 'Платить', 'Ödemek'),
(460, 'paystack', 'Paystack', 'Paystack', 'Paystack', 'Paystack', 'Paystack', 'Paystack'),
(461, 'cashfree', 'Cashfree', 'Cashfree', 'Sans argent', 'Bargeldlos', 'Cashfree', 'Nakitsiz'),
(462, 'razorpay', 'Razorpay', 'رازورباي', 'Razorpay', 'Razorpay', 'Razorpay', 'Razorpay'),
(463, 'paysera', 'Paysera', 'Paysera', 'Paysera', 'Paysera', 'Paysera', 'Paysera'),
(464, 'iyzipay', 'Iyzipay', 'إيزيباي', 'Iyzipay', 'Iyzipay', 'Айзипай', 'İyzipay'),
(465, 'bank_transfer', 'Bank transfer', 'التحويل المصرفي', 'virement', 'Banküberweisung', 'банковский перевод', 'banka transferi'),
(466, 'drop_img_here', 'Drop Image Here', 'إسقاط الصورة هنا', 'Déposer l\'image ici', 'Bild hier ablegen', 'Перетащите изображение сюда', 'Resmi Buraya Bırakın'),
(467, 'or', 'or', 'أو', 'ou', 'oder', 'или', 'veya'),
(468, 'browse_to_upload', 'Browse To Upload', 'تصفح للتحميل', 'Parcourir pour télécharger', 'Zum Hochladen navigieren', 'Обзор для загрузки', 'Yüklemeye Göz At'),
(469, 'file_not_supported', 'file not supported', 'الملف غير مدعوم', 'fichier non pris en charge', 'Datei wird nicht unterstützt', 'файл не поддерживается', 'dosya desteklenmiyor'),
(470, 'bank_transfer_request', 'Your request has been successfully sent, we will notify you once it&#039;s approved', 'تم إرسال طلبك بنجاح ، وسنخطرك بمجرد الموافقة عليه', 'Votre demande a été envoyée avec succès, nous vous en informerons une fois qu\'elle sera approuvée', 'Ihre Anfrage wurde erfolgreich gesendet. Wir werden Sie benachrichtigen, sobald sie genehmigt wurde', 'Ваш запрос был успешно отправлен, мы сообщим вам, как только он будет одобрен', 'Talebiniz başarıyla gönderildi, onaylandığında sizi bilgilendireceğiz'),
(471, 'live_video', 'Live Video', 'فيديو مباشر', 'Vidéo en direct', 'Live-Video', 'Живое видео', 'Canlı video'),
(472, 'flip', 'Flip', 'يواجه', 'Retourner', 'Flip', 'кувырок', 'Çevir'),
(473, 'price', 'Price', 'السعر', 'Prix', 'Preis', 'Цена', 'Fiyat'),
(474, 'price_can_not_be_empty', 'Price can not be empty', 'لا يمكن أن يكون السعر فارغًا', 'Le prix ne peut pas être vide', 'Preis kann nicht leer sein', 'Цена не может быть пустой', 'Fiyat boş olamaz'),
(475, 'live', 'Live', 'حي', 'Vivre', 'Leben', 'Прямой эфир', 'Canlı'),
(476, 'end_live', 'End Live', 'إنهاء مباشر', 'Fin en direct', 'Live beenden', 'Конец жизни', 'Canlı Bitir'),
(477, 'go_live', 'Go Live', 'انطلق مباشرة', 'Passez en direct', 'Geh Leben', 'Go Live', 'Canlı Yayına Geç'),
(478, 'stream_has_ended', 'stream has ended', 'انتهى الدفق', 'le flux est terminé', 'Stream ist beendet', 'поток закончился', 'akış sona erdi'),
(479, 'joined_live_video', 'joined live video', 'انضم إلى الفيديو المباشر', 'rejoint la vidéo en direct', 'Live-Video beigetreten', 'присоединился к живому видео', 'canlı videoya katıldı'),
(480, 'left_live_video', 'left live video', 'غادر فيديو مباشر', 'vidéo en direct gauche', 'Live-Video verlassen', 'осталось живое видео', 'canlı videodan ayrıldı'),
(481, 'offline', 'offline', 'غير متصل على الانترنت', 'hors ligne', 'offline', 'не в сети', 'çevrimdışı'),
(482, 'paypal', 'paypal', 'باي بال', 'Pay Pal', 'Paypal', 'PayPal', 'paypal'),
(483, 'pages', 'Pages', 'الصفحات', 'Des pages', 'Seiten', 'Страницы', 'Sayfalar'),
(484, 'night_mode', 'Night mode', 'وضع الليل', 'Mode nuit', 'Nacht-Modus', 'Ночной режим', 'Gece modu'),
(485, 'day_mode', 'Day mode', 'وضع اليوم', 'Mode jour', 'Tagesmodus', 'Дневной режим', 'Gündüz modu');

-- --------------------------------------------------------

--
-- Table structure for table `fl_lists`
--

CREATE TABLE `fl_lists` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT 0,
  `title` text COLLATE utf8_unicode_ci DEFAULT NULL,
  `short_title` text COLLATE utf8_unicode_ci DEFAULT NULL,
  `slug` text COLLATE utf8_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8_unicode_ci DEFAULT NULL,
  `tags` text COLLATE utf8_unicode_ci DEFAULT NULL,
  `category` int(11) NOT NULL DEFAULT 0,
  `image` varchar(150) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `viewable` enum('0','1') COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `shares` int(11) NOT NULL DEFAULT 0,
  `time` int(11) NOT NULL DEFAULT 0,
  `last_update` int(11) NOT NULL DEFAULT 0,
  `entries_per_page` int(11) NOT NULL DEFAULT 0,
  `views` int(11) NOT NULL DEFAULT 0,
  `featured` int(11) NOT NULL DEFAULT 0,
  `registered` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0/0000',
  `hd` int(11) NOT NULL DEFAULT 0,
  `active` enum('0','1') COLLATE utf8_unicode_ci NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fl_live_sub_users`
--

CREATE TABLE `fl_live_sub_users` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT 0,
  `post_id` int(11) NOT NULL DEFAULT 0,
  `is_watching` int(11) NOT NULL DEFAULT 0,
  `time` int(50) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `fl_music`
--

CREATE TABLE `fl_music` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` text COLLATE utf8_unicode_ci DEFAULT NULL,
  `short_title` text COLLATE utf8_unicode_ci DEFAULT NULL,
  `slug` text COLLATE utf8_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8_unicode_ci DEFAULT NULL,
  `tags` text COLLATE utf8_unicode_ci DEFAULT NULL,
  `category` int(11) NOT NULL DEFAULT 0,
  `image` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `viewable` enum('0','1') COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `shares` int(11) NOT NULL DEFAULT 0,
  `time` int(11) NOT NULL DEFAULT 0,
  `last_update` int(11) NOT NULL DEFAULT 0,
  `entries_per_page` int(32) NOT NULL DEFAULT 0,
  `views` int(11) NOT NULL DEFAULT 0,
  `featured` int(11) NOT NULL DEFAULT 0,
  `registered` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0/0000',
  `hd` int(11) NOT NULL DEFAULT 0,
  `active` enum('0','1') COLLATE utf8_unicode_ci NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fl_news`
--

CREATE TABLE `fl_news` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` text COLLATE utf8_unicode_ci DEFAULT NULL,
  `short_title` text COLLATE utf8_unicode_ci DEFAULT NULL,
  `slug` text COLLATE utf8_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8_unicode_ci DEFAULT NULL,
  `tags` text COLLATE utf8_unicode_ci DEFAULT NULL,
  `category` int(11) NOT NULL DEFAULT 0,
  `image` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `viewable` enum('0','1') COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `shares` int(11) NOT NULL DEFAULT 0,
  `time` int(11) NOT NULL DEFAULT 0,
  `last_update` int(11) NOT NULL DEFAULT 0,
  `entries_per_page` int(32) NOT NULL DEFAULT 0,
  `views` int(11) NOT NULL DEFAULT 0,
  `featured` int(11) NOT NULL DEFAULT 0,
  `registered` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0/0000',
  `hd` int(11) NOT NULL DEFAULT 0,
  `active` enum('0','1') COLLATE utf8_unicode_ci NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fl_payments`
--

CREATE TABLE `fl_payments` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT 0,
  `type` varchar(200) NOT NULL DEFAULT '',
  `amount` int(11) NOT NULL DEFAULT 0,
  `date` varchar(100) NOT NULL DEFAULT '',
  `expire` varchar(30) NOT NULL DEFAULT '',
  `time` int(20) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `fl_polls`
--

CREATE TABLE `fl_polls` (
  `id` int(11) NOT NULL,
  `entry_id` int(11) NOT NULL DEFAULT 0,
  `text` varchar(200) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `image` varchar(120) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `time` int(11) NOT NULL DEFAULT 0,
  `type` varchar(10) CHARACTER SET utf8 NOT NULL DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fl_poll_pages`
--

CREATE TABLE `fl_poll_pages` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT 0,
  `title` text COLLATE utf8_unicode_ci DEFAULT NULL,
  `short_title` text COLLATE utf8_unicode_ci DEFAULT NULL,
  `slug` text COLLATE utf8_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8_unicode_ci DEFAULT NULL,
  `tags` text COLLATE utf8_unicode_ci DEFAULT NULL,
  `category` int(11) NOT NULL DEFAULT 0,
  `image` varchar(150) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `viewable` enum('0','1') COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `shares` int(11) NOT NULL DEFAULT 0,
  `time` int(11) NOT NULL DEFAULT 0,
  `last_update` int(11) NOT NULL DEFAULT 0,
  `entries_per_page` int(11) NOT NULL DEFAULT 0,
  `views` int(11) NOT NULL DEFAULT 0,
  `featured` int(11) NOT NULL DEFAULT 0,
  `registered` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0/0000',
  `hd` int(11) NOT NULL DEFAULT 0,
  `active` enum('0','1') COLLATE utf8_unicode_ci NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fl_profile_fields`
--

CREATE TABLE `fl_profile_fields` (
  `id` int(11) NOT NULL,
  `name` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `description` text COLLATE utf8_unicode_ci DEFAULT NULL,
  `type` text COLLATE utf8_unicode_ci DEFAULT NULL,
  `length` int(11) NOT NULL DEFAULT 0,
  `placement` varchar(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'profile',
  `registration_page` int(11) NOT NULL DEFAULT 0,
  `profile_page` int(11) NOT NULL DEFAULT 0,
  `select_type` varchar(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'none',
  `active` enum('0','1') COLLATE utf8_unicode_ci NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fl_quizzes`
--

CREATE TABLE `fl_quizzes` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` text COLLATE utf8_unicode_ci DEFAULT NULL,
  `short_title` text COLLATE utf8_unicode_ci DEFAULT NULL,
  `slug` text COLLATE utf8_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8_unicode_ci DEFAULT NULL,
  `tags` text COLLATE utf8_unicode_ci DEFAULT NULL,
  `category` int(11) NOT NULL DEFAULT 0,
  `image` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `viewable` enum('0','1') COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `shares` int(11) NOT NULL DEFAULT 0,
  `time` int(11) NOT NULL DEFAULT 0,
  `last_update` int(11) NOT NULL DEFAULT 0,
  `entries_per_page` int(32) NOT NULL DEFAULT 0,
  `views` int(11) NOT NULL DEFAULT 0,
  `featured` int(11) NOT NULL DEFAULT 0,
  `registered` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0/0000',
  `hd` int(11) NOT NULL DEFAULT 0,
  `active` enum('0','1') COLLATE utf8_unicode_ci NOT NULL DEFAULT '1'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fl_quiz_answers`
--

CREATE TABLE `fl_quiz_answers` (
  `id` int(11) NOT NULL,
  `entry_id` int(11) NOT NULL DEFAULT 0,
  `result_index` int(11) NOT NULL DEFAULT 0,
  `text` varchar(3000) NOT NULL DEFAULT '',
  `image` varchar(5000) NOT NULL DEFAULT '',
  `time` varchar(50) NOT NULL DEFAULT '0',
  `type` varchar(30) DEFAULT '',
  `correct` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `fl_reports`
--

CREATE TABLE `fl_reports` (
  `id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL DEFAULT 0,
  `profile_id` int(11) NOT NULL DEFAULT 0,
  `page_id` int(15) NOT NULL DEFAULT 0,
  `user_id` int(11) NOT NULL DEFAULT 0,
  `text` text DEFAULT NULL,
  `type` varchar(30) NOT NULL DEFAULT '',
  `seen` int(11) NOT NULL DEFAULT 0,
  `time` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `fl_sessions`
--

CREATE TABLE `fl_sessions` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT 0,
  `session_id` varchar(140) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `platform` varchar(20) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'web',
  `time` int(11) NOT NULL DEFAULT 0
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fl_terms`
--

CREATE TABLE `fl_terms` (
  `id` int(11) NOT NULL,
  `type` varchar(32) NOT NULL DEFAULT '',
  `text` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `fl_terms`
--

INSERT INTO `fl_terms` (`id`, `type`, `text`) VALUES
(1, 'terms_of_use', '<h4>1- Write your Terms Of Use here.</h4>       gh   \nLorem ipsum dolor sit amet, consectetur adisdpisicing elit, sed do eiusmod          tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,          quis sdnostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo          consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse          cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non          proident, sunt in culpa qui officia deserunt mollit anim id est laborum.          <br><br>          <h4>2- Random title</h4>          Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod          tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,          quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo          consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse          cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non          proident, sunt in culpa qui officia deserunt mollit anim id est laborum.sdsd'),
(2, 'privacy_policy', ' <h4>1- Write your Privacy Policy here.</h4>          Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod          tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,          quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo          consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse          cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non          proident, sunt in culpa qui officia deserunt mollit anim id est laborum.          <br><br>          <h4>2- Random title</h4>          Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod          tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,          quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo          consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse          cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non          proident, sunt in culpa qui officia deserunt mollit anim id est laborum.sdsd'),
(3, 'about', '<h4>1- Write about your website here.</h4>          Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod          tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,          quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo          consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse          cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non          proident, sunt in culpa qui officia deserunt mollit anim id est laborum.          <br><br>          <h4>2- Random title</h4>          Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod          tempor incididunt ut labore et dxzcolore magna aliqua. Ut enim ad minim veniam,          quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo          consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse          cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non          proident, sunt in culpa qui officia deserunt mollit anim id est laborum.sdsdsd');

-- --------------------------------------------------------

--
-- Table structure for table `fl_uc_fields`
--

CREATE TABLE `fl_uc_fields` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT 0,
  `fid_3` varchar(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `fid_1` varchar(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `fid_2` varchar(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fl_users`
--

CREATE TABLE `fl_users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(32) CHARACTER SET latin1 NOT NULL DEFAULT '',
  `email` varchar(52) CHARACTER SET latin1 NOT NULL DEFAULT '',
  `first_name` varchar(35) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `last_name` varchar(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `password` varchar(52) CHARACTER SET latin1 NOT NULL DEFAULT '',
  `email_code` varchar(35) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `avatar` varchar(100) CHARACTER SET latin1 NOT NULL DEFAULT 'upload/photos/avatar.jpg',
  `cover` varchar(100) CHARACTER SET latin1 NOT NULL DEFAULT 'upload/photos/cover.jpg',
  `country_id` int(11) NOT NULL DEFAULT 0,
  `gender` varchar(10) CHARACTER SET latin1 NOT NULL DEFAULT 'male',
  `about` text COLLATE utf8_unicode_ci DEFAULT NULL,
  `google` varchar(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `facebook` varchar(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `twitter` varchar(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `ip_address` varchar(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0.0.0.0',
  `timezone` varchar(50) CHARACTER SET utf8 NOT NULL DEFAULT 'UTC',
  `language` varchar(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'english',
  `device_id` varchar(100) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `last_active` int(11) NOT NULL DEFAULT 0,
  `src` varchar(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `verified` int(11) NOT NULL DEFAULT 0,
  `admin` enum('0','1','2') CHARACTER SET latin1 NOT NULL DEFAULT '0',
  `registered` varchar(32) CHARACTER SET latin1 NOT NULL DEFAULT '00/0000',
  `time` int(20) NOT NULL DEFAULT 0,
  `active` enum('0','1','2') CHARACTER SET latin1 NOT NULL DEFAULT '0',
  `is_pro` int(15) NOT NULL DEFAULT 0,
  `posts` int(11) NOT NULL DEFAULT 0,
  `wallet` decimal(13,2) NOT NULL DEFAULT 0.00,
  `paystack_ref` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `ConversationId` varchar(50) CHARACTER SET utf8 NOT NULL DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fl_user_ads`
--

CREATE TABLE `fl_user_ads` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT 0,
  `title` varchar(500) NOT NULL DEFAULT '',
  `url` varchar(3000) NOT NULL DEFAULT '',
  `placement` varchar(150) NOT NULL DEFAULT '',
  `status` int(5) NOT NULL DEFAULT 1,
  `spent` decimal(13,2) NOT NULL DEFAULT 0.00,
  `results` int(11) NOT NULL DEFAULT 0,
  `media_file` varchar(3000) NOT NULL DEFAULT '',
  `time` varchar(100) NOT NULL DEFAULT '0',
  `type` varchar(100) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `fl_user_reactions`
--

CREATE TABLE `fl_user_reactions` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT 0,
  `post_id` int(11) NOT NULL DEFAULT 0,
  `page` varchar(10) NOT NULL DEFAULT '',
  `ip_address` varchar(100) NOT NULL DEFAULT '',
  `option_id` int(11) NOT NULL DEFAULT 0,
  `time` varchar(50) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `fl_verification_requests`
--

CREATE TABLE `fl_verification_requests` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT 0,
  `name` varchar(300) NOT NULL DEFAULT '',
  `message` text DEFAULT NULL,
  `passport` varchar(3000) NOT NULL DEFAULT '',
  `photo` varchar(3000) NOT NULL DEFAULT '',
  `type` varchar(100) NOT NULL DEFAULT 'user',
  `time` varchar(100) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `fl_videos`
--

CREATE TABLE `fl_videos` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` text COLLATE utf8_unicode_ci DEFAULT NULL,
  `short_title` text COLLATE utf8_unicode_ci DEFAULT NULL,
  `slug` text COLLATE utf8_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8_unicode_ci DEFAULT NULL,
  `tags` text COLLATE utf8_unicode_ci DEFAULT NULL,
  `category` int(11) NOT NULL DEFAULT 0,
  `image` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `viewable` enum('0','1') COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `shares` int(11) NOT NULL DEFAULT 0,
  `time` int(11) NOT NULL DEFAULT 0,
  `last_update` int(11) NOT NULL DEFAULT 0,
  `entries_per_page` int(32) NOT NULL DEFAULT 0,
  `views` int(11) NOT NULL DEFAULT 0,
  `featured` int(11) NOT NULL DEFAULT 0,
  `registered` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0/0000',
  `hd` int(11) NOT NULL DEFAULT 0,
  `active` enum('0','1') COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `type` varchar(50) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `stream_name` varchar(100) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `live_time` int(50) NOT NULL DEFAULT 0,
  `live_ended` int(11) NOT NULL DEFAULT 0,
  `agora_resource_id` text CHARACTER SET utf8 DEFAULT NULL,
  `agora_sid` varchar(500) CHARACTER SET utf8 NOT NULL DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fl_votes`
--

CREATE TABLE `fl_votes` (
  `id` int(11) NOT NULL,
  `option_id` int(11) NOT NULL DEFAULT 0,
  `entry_id` int(11) NOT NULL DEFAULT 0,
  `ip_address` varchar(32) NOT NULL DEFAULT '0.0.0.0',
  `user_id` int(11) NOT NULL DEFAULT 0,
  `time` int(11) NOT NULL DEFAULT 0
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `fl_admininvitations`
--
ALTER TABLE `fl_admininvitations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `code` (`code`(255));

--
-- Indexes for table `fl_ads`
--
ALTER TABLE `fl_ads`
  ADD PRIMARY KEY (`id`),
  ADD KEY `active` (`active`),
  ADD KEY `type` (`type`);

--
-- Indexes for table `fl_announcement`
--
ALTER TABLE `fl_announcement`
  ADD PRIMARY KEY (`id`),
  ADD KEY `active` (`active`);

--
-- Indexes for table `fl_announcement_views`
--
ALTER TABLE `fl_announcement_views`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `announcement_id` (`announcement_id`);

--
-- Indexes for table `fl_bank_receipts`
--
ALTER TABLE `fl_bank_receipts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `fl_banned_ip`
--
ALTER TABLE `fl_banned_ip`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ip_address` (`ip_address`);

--
-- Indexes for table `fl_br_news`
--
ALTER TABLE `fl_br_news`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `fl_comments`
--
ALTER TABLE `fl_comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `news_id` (`news_id`);

--
-- Indexes for table `fl_comm_replies`
--
ALTER TABLE `fl_comm_replies`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `fl_config`
--
ALTER TABLE `fl_config`
  ADD PRIMARY KEY (`id`),
  ADD KEY `value` (`value`(767)),
  ADD KEY `name` (`name`);

--
-- Indexes for table `fl_custompages`
--
ALTER TABLE `fl_custompages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `fl_entries`
--
ALTER TABLE `fl_entries`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `post_id` (`post_id`),
  ADD KEY `entry_type` (`entry_type`),
  ADD KEY `entry_page` (`entry_page`),
  ADD KEY `index_id` (`index_id`);

--
-- Indexes for table `fl_fav`
--
ALTER TABLE `fl_fav`
  ADD PRIMARY KEY (`id`),
  ADD KEY `post_id` (`post_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `page` (`page`),
  ADD KEY `time` (`time`);

--
-- Indexes for table `fl_langs`
--
ALTER TABLE `fl_langs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `lang_key` (`lang_key`);

--
-- Indexes for table `fl_lists`
--
ALTER TABLE `fl_lists`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `category` (`category`),
  ADD KEY `active` (`active`),
  ADD KEY `viewable` (`viewable`),
  ADD KEY `featured` (`featured`),
  ADD KEY `hd` (`hd`);
ALTER TABLE `fl_lists` ADD FULLTEXT KEY `short_title` (`short_title`);
ALTER TABLE `fl_lists` ADD FULLTEXT KEY `title_2` (`title`,`description`);

--
-- Indexes for table `fl_live_sub_users`
--
ALTER TABLE `fl_live_sub_users`
  ADD PRIMARY KEY (`id`),
  ADD KEY `time` (`time`),
  ADD KEY `is_watching` (`is_watching`),
  ADD KEY `post_id` (`post_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `fl_music`
--
ALTER TABLE `fl_music`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `category` (`category`),
  ADD KEY `active` (`active`),
  ADD KEY `featured` (`featured`),
  ADD KEY `hd` (`hd`);
ALTER TABLE `fl_music` ADD FULLTEXT KEY `slug` (`slug`);
ALTER TABLE `fl_music` ADD FULLTEXT KEY `short_title` (`short_title`);
ALTER TABLE `fl_music` ADD FULLTEXT KEY `title` (`title`,`description`);

--
-- Indexes for table `fl_news`
--
ALTER TABLE `fl_news`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `category` (`category`),
  ADD KEY `active` (`active`),
  ADD KEY `featured` (`featured`),
  ADD KEY `hd` (`hd`);
ALTER TABLE `fl_news` ADD FULLTEXT KEY `slug` (`slug`);
ALTER TABLE `fl_news` ADD FULLTEXT KEY `short_title` (`short_title`);
ALTER TABLE `fl_news` ADD FULLTEXT KEY `title_2` (`title`,`description`);

--
-- Indexes for table `fl_payments`
--
ALTER TABLE `fl_payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `expire` (`expire`);

--
-- Indexes for table `fl_polls`
--
ALTER TABLE `fl_polls`
  ADD PRIMARY KEY (`id`),
  ADD KEY `entry_id` (`entry_id`);

--
-- Indexes for table `fl_poll_pages`
--
ALTER TABLE `fl_poll_pages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `category` (`category`),
  ADD KEY `active` (`active`),
  ADD KEY `viewable` (`viewable`),
  ADD KEY `featured` (`featured`),
  ADD KEY `hd` (`hd`);
ALTER TABLE `fl_poll_pages` ADD FULLTEXT KEY `short_title` (`short_title`);
ALTER TABLE `fl_poll_pages` ADD FULLTEXT KEY `title` (`title`,`description`);

--
-- Indexes for table `fl_profile_fields`
--
ALTER TABLE `fl_profile_fields`
  ADD PRIMARY KEY (`id`),
  ADD KEY `registration_page` (`registration_page`),
  ADD KEY `active` (`active`),
  ADD KEY `profile_page` (`profile_page`);

--
-- Indexes for table `fl_quizzes`
--
ALTER TABLE `fl_quizzes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `category` (`category`),
  ADD KEY `active` (`active`),
  ADD KEY `featured` (`featured`),
  ADD KEY `hd` (`hd`);
ALTER TABLE `fl_quizzes` ADD FULLTEXT KEY `slug` (`slug`);
ALTER TABLE `fl_quizzes` ADD FULLTEXT KEY `short_title` (`short_title`);
ALTER TABLE `fl_quizzes` ADD FULLTEXT KEY `title_2` (`title`,`description`);

--
-- Indexes for table `fl_quiz_answers`
--
ALTER TABLE `fl_quiz_answers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `entry_id` (`entry_id`),
  ADD KEY `result_index` (`result_index`);

--
-- Indexes for table `fl_reports`
--
ALTER TABLE `fl_reports`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `post_id` (`post_id`),
  ADD KEY `seen` (`seen`),
  ADD KEY `profile_id` (`profile_id`),
  ADD KEY `page_id` (`page_id`),
  ADD KEY `type` (`type`);

--
-- Indexes for table `fl_sessions`
--
ALTER TABLE `fl_sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `session_id` (`session_id`),
  ADD KEY `platform` (`platform`),
  ADD KEY `time` (`time`);

--
-- Indexes for table `fl_terms`
--
ALTER TABLE `fl_terms`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `fl_uc_fields`
--
ALTER TABLE `fl_uc_fields`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `fl_users`
--
ALTER TABLE `fl_users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `active` (`active`),
  ADD KEY `admin` (`admin`),
  ADD KEY `last_active` (`last_active`),
  ADD KEY `is_pro` (`is_pro`),
  ADD KEY `wallet` (`wallet`),
  ADD KEY `time` (`time`);

--
-- Indexes for table `fl_user_ads`
--
ALTER TABLE `fl_user_ads`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `status` (`status`);

--
-- Indexes for table `fl_user_reactions`
--
ALTER TABLE `fl_user_reactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `fl_verification_requests`
--
ALTER TABLE `fl_verification_requests`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `fl_videos`
--
ALTER TABLE `fl_videos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `category` (`category`),
  ADD KEY `active` (`active`),
  ADD KEY `featured` (`featured`),
  ADD KEY `hd` (`hd`);
ALTER TABLE `fl_videos` ADD FULLTEXT KEY `slug` (`slug`);
ALTER TABLE `fl_videos` ADD FULLTEXT KEY `short_title` (`short_title`);
ALTER TABLE `fl_videos` ADD FULLTEXT KEY `title` (`title`,`description`);

--
-- Indexes for table `fl_votes`
--
ALTER TABLE `fl_votes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `option_id` (`option_id`),
  ADD KEY `ip_address` (`ip_address`),
  ADD KEY `entry_id` (`entry_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `fl_admininvitations`
--
ALTER TABLE `fl_admininvitations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fl_ads`
--
ALTER TABLE `fl_ads`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `fl_announcement`
--
ALTER TABLE `fl_announcement`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fl_announcement_views`
--
ALTER TABLE `fl_announcement_views`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fl_bank_receipts`
--
ALTER TABLE `fl_bank_receipts`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fl_banned_ip`
--
ALTER TABLE `fl_banned_ip`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fl_br_news`
--
ALTER TABLE `fl_br_news`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fl_comments`
--
ALTER TABLE `fl_comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fl_comm_replies`
--
ALTER TABLE `fl_comm_replies`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fl_config`
--
ALTER TABLE `fl_config`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=144;

--
-- AUTO_INCREMENT for table `fl_custompages`
--
ALTER TABLE `fl_custompages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fl_entries`
--
ALTER TABLE `fl_entries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fl_fav`
--
ALTER TABLE `fl_fav`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fl_langs`
--
ALTER TABLE `fl_langs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=486;

--
-- AUTO_INCREMENT for table `fl_lists`
--
ALTER TABLE `fl_lists`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fl_live_sub_users`
--
ALTER TABLE `fl_live_sub_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fl_music`
--
ALTER TABLE `fl_music`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fl_news`
--
ALTER TABLE `fl_news`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fl_payments`
--
ALTER TABLE `fl_payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fl_polls`
--
ALTER TABLE `fl_polls`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fl_poll_pages`
--
ALTER TABLE `fl_poll_pages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fl_profile_fields`
--
ALTER TABLE `fl_profile_fields`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fl_quizzes`
--
ALTER TABLE `fl_quizzes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fl_quiz_answers`
--
ALTER TABLE `fl_quiz_answers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fl_reports`
--
ALTER TABLE `fl_reports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fl_sessions`
--
ALTER TABLE `fl_sessions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fl_terms`
--
ALTER TABLE `fl_terms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `fl_uc_fields`
--
ALTER TABLE `fl_uc_fields`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fl_users`
--
ALTER TABLE `fl_users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fl_user_ads`
--
ALTER TABLE `fl_user_ads`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fl_user_reactions`
--
ALTER TABLE `fl_user_reactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fl_verification_requests`
--
ALTER TABLE `fl_verification_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fl_videos`
--
ALTER TABLE `fl_videos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fl_votes`
--
ALTER TABLE `fl_votes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
