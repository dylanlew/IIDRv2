<?php

// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Automatically generated strings for Moodle 2.0 installer
 *
 * Do not edit this file manually! It contains just a subset of strings
 * needed during the very first steps of installation. This file was
 * generated automatically by export-installer.php (which is part of AMOS
 * {@link http://docs.moodle.org/dev/Languages/AMOS}) using the
 * list of strings defined in /install/stringnames.txt.
 *
 * @package   installer
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['admindirname'] = 'Админ директор';
$string['availablelangs'] = 'Боломжит хэлний багцууд';
$string['chooselanguagehead'] = 'Хэлээ сонго';
$string['chooselanguagesub'] = 'Зөвхөн суулгах үед ашиглагдах хэлээ сонго. Та дараа нь сайтны болон хэрэглэгчийн хэлийг сонгож болно.';
$string['dataroot'] = 'Өгөгдлийн директор';
$string['dbprefix'] = 'Хүснэгтний угтвар';
$string['dirroot'] = 'Моодл хавтас';
$string['environmenthead'] = 'Таны орчиныг шалгаж байна ...';
$string['installation'] = 'Суулгах';
$string['langdownloaderror'] = 'Харамсалтай нь "{$a}" хэл суусангүй. Суулгах үйл ажиллагаа Англи хэл дээр үргэлжлэх болно.';
$string['memorylimithelp'] = '<p>Таны серверийн PHP санах ойн хязгаар нь {$a} гэж тохируулсан байна.</p>

<p>Энэ нь Моодл сүүлд санах ойн асуудлууд ялангуяа олон модуль ба/эсвэл олон хэрэглэгч идэвхжүүлсэн үед учирч болзошгүй. </p>

<p>Бид танд зөвлөхөд РНР-гээ боломжит дээд хязгаартайгаар жишээ нь 16М болгож тохируул. Үүний тулд хэд хэдэн арга байна: </p>
<ol>
<li>Хэрвээ та PHP-г <i>--enable-memory-limit</i>-тай дахин хөрвүүлэх боломжтой бол. Энэ нь Моодлийг санах ойн хязгаарыг өөрөө тогтоох боломжтой болгоно. </li>
<li>Хэрвээ та php.ini файлдаа хандаж чадвал, <b>memory_limit</b> гэсэн тохиргоог 16M гэх мэтээр өөрчилж болно. Хэрвээ хандалт байхгүй бол админаараа үүнийг хийлгэнэ үү.</li>
<li>Зарим PHP сервер дээр Моодл хавтсан дотроо дараах мөр агуулсан .htaccess файл үүсгэж болно: <p><blockquote>php_value memory_limit 16M</blockquote></p>
<p>Зарим сервер дээр энэ нь <b>бүх</b> PHP хуудсуудыг ажиллахгүй болгоно (та хуудаснууд руу орох юм бол алдааг нь харах болно). Тиймээс .htaccess файлыг устгах хэрэгтэй болно.</p></li>
</ol>';
$string['phpversion'] = 'РНР хувилбар';
$string['phpversionhelp'] = '<p>Mooдл нь РНР хувилбарын хамгийн багадаа 4.3.0 эсвэл 5.1.0 байхыг шаарддаг (5.0.x нь алдаануудтай).</p>
<p>Та одоо {$a} хувилбар ажиллуулж байна</p>
<p>Та PHP-гээ шинэчлэх эсвэл PHP-ийн шинэ хувилбар бүхий хост руу шилжүүл!<br/>
(5.0.x тохиолдолд доошоо 4.4.x хувилбар руу орж болно)</p>';
$string['welcomep10'] = '{$a->installername} ({$a->installerversion})';
$string['welcomep20'] = 'Та <strong>{$a->packname} {$a->packversion}</strong> багцыг компьютер дээрээ амжилттай суулгаж ажиллуулсан тул энэ хуудсыг харж байна. Баяр хүргэе!';
$string['welcomep30'] = '<strong>{$a->installername}</strong> -ний энэ хувилбар нь <strong>Mooдл</strong> ажиллах орчныг үүсгэх програмууд агуулсан, тэдгээр нь:';
$string['welcomep40'] = 'Энэ багц нь мөн <strong>Mooдл {$a->moodlerelease} ({$a->moodleversion})</strong>-г агуулсан.';
$string['welcomep50'] = 'Энэ багц доторх бүх програмууд нь өөрсдийн лизенцтэй. Бүрэн <strong>{$a->installername}</strong> багц нь 
<a href="http://www.opensource.org/docs/definition_plain.html">open source</a>  бөгөөд <a href="http://www.gnu.org/copyleft/gpl.html">GPL</a> -ийн лизенцийн дор түгээгддэг.';
$string['welcomep60'] = 'Дараагийн хуудсууд нь компьютер дээрээ <strong>Mooдл</strong> тохируулах тааруулах энгийн алхмуудыг агуулсан байгаа. Та анхны утгын тохиргоонуудыг хүлээн авч болно, эсвэл өөрийн хэрэгцээнд нийцүүлэн өөрчилж болно.';
$string['welcomep70'] = '<strong>Mooдлээ</strong> тохируулахын тулд доорх “Үргэлжлүүлэх” товчин дээр дарж цааж явна уу.';
$string['wwwroot'] = 'Вэб хаяг';
