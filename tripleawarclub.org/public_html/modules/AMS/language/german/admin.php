<?php
// $Id: admin.php,v 1.18 2004/07/26 17:51:25 hthouzard Exp $
//%%%%%%	Admin Module Name  Artikel 	%%%%%
define("_AMS_AM_DBUPDATED","Datenkank erfolgreich aktualisiert!");
define("_AMS_AM_CONFIG","AMS Konfiguration");
define("_AMS_AM_AUTOARTICLES","Automatisierte Artikel");
define("_AMS_AM_STORYID","Story ID");
define("_AMS_AM_TITLE","Titel");
define("_AMS_AM_TOPIC","Thema");
define("_AMS_AM_ARTICLE", "Artikel");
define("_AMS_AM_POSTER","Verfasser");
define("_AMS_AM_PROGRAMMED","Programmierte(s) Datum/Zeit");
define("_AMS_AM_ACTION","Aktion");
define("_AMS_AM_EDIT","Bearbeiten");
define("_AMS_AM_DELETE","L�schen");
define("_AMS_AM_LAST10ARTS","Letzte %d Artikel");
define("_AMS_AM_PUBLISHED","Ver�ffentlicht"); // Ver�ffentlicht Datum
define("_AMS_AM_GO","Ausf�hren!");
define("_AMS_AM_EDITARTICLE","Bearbeite Artikel");
define("_AMS_AM_POSTNEWARTICLE","Schreibe neuen Artikel");
define("_AMS_AM_ARTPUBLISHED","Ihr Artikel wurde ver�ffentlicht!");
define("_AMS_AM_HELLO","Hallo %s,");
define("_AMS_AM_YOURARTPUB","Ihr eingeschickter Artikel wurde ver�ffentlicht.");
define("_AMS_AM_TITLEC","Titel: ");
define("_AMS_AM_URLC","URL: ");
define("_AMS_AM_PUBLISHEDC","Ver�ffentlicht: ");
define("_AMS_AM_RUSUREDEL","Sind Sie siecher, das Sie diesen Artikel und alle verbundenen Kommentare l�schen wollen?");
define("_AMS_AM_YES","Ja");
define("_AMS_AM_NO","Nein");
define("_AMS_AM_INTROTEXT","Einleitungs Text");
define("_AMS_AM_EXTEXT","Erweiterter Text");
define("_AMS_AM_ALLOWEDHTML","Erlaube HTML:");
define("_AMS_AM_DISAMILEY","Deaktiviere Smiley");
define("_AMS_AM_DISHTML","Deaktiviere HTML");
define("_AMS_AM_APPROVE","Genehmige");
define("_AMS_AM_MOVETOTOP","Diesen Artikel nach oben verschieben");
define("_AMS_AM_CHANGEDATETIME","Datum/Zeit der Publikation ver�ndern");
define("_AMS_AM_NOWSETTIME","Ist jetzt gesetzt auf: %s"); // %s is datetime of publish
define("_AMS_AM_CURRENTTIME","Aktuelle Zeit ist: %s");  // %s is the current datetime
define("_AMS_AM_SETDATETIME","Setze Datum/Zeit der Ver�ffentlichung");
define("_AMS_AM_MONTHC","Monat:");
define("_AMS_AM_DAYC","Tag:");
define("_AMS_AM_YEARC","Jahr:");
define("_AMS_AM_TIMEC","Zeit:");
define("_AMS_AM_PREVIEW","Vorschau");
define("_AMS_AM_SAVE","Speichern");
define("_AMS_AM_PUBINHOME","Auf der Startseite ver�ffentlichen?");
define("_AMS_AM_ADD","Hinzuf�gen");

//%%%%%%	Admin Module Name  Themas 	%%%%%

define("_AMS_AM_ADDMTOPIC","Ein HAUPT-Thema hinzuf�gen");
define("_AMS_AM_TOPICNAME","Themen Name");
define("_AMS_AM_MAX40CHAR","(max: 40 Zeichen)");
define("_AMS_AM_TOPICIMG","Themen Bild");
define("_AMS_AM_IMGNAEXLOC","Bildname + Erweiterung ist zu finden in %s");
define("_AMS_AM_FEXAMPLE","zum Beispiel: games.gif");
define("_AMS_AM_ADDSUBTOPIC","Ein SUB-Thema hinzuf�gen");
define("_AMS_AM_IN","in");
define("_AMS_AM_MODIFYTOPIC","Thema �ndern");
define("_AMS_AM_MODIFY","�ndere");
define("_AMS_AM_PARENTTOPIC","�bergeordnetes Thema");
define("_AMS_AM_SAVECHANGE","�nderungen speichern");
define("_AMS_AM_DEL","L�schen");
define("_AMS_AM_CANCEL","Abbrechen");
define("_AMS_AM_WAYSYWTDTTAL","WARNUNG: Sind Sie sicher, das Sie dieses Thema und alle verbundenen Artikel und Kommentare l�schen wollen?");


// Added in Beta6
define("_AMS_AM_TOPICSMNGR","Themen Manager");
define("_AMS_AM_PEARTICLES","Artikel Manager");
define("_AMS_AM_NEWSUB","Neue Einreichungen");
define("_AMS_AM_POSTED","Eingeschickt");
define("_AMS_AM_GENERALCONF","Generelle Konfiguration");

// Added in RC2
define("_AMS_AM_TOPICDISPLAY","Bild zeigen?");
define("_AMS_AM_TOPICALIGN","Position");
define("_AMS_AM_RIGHT","Rechts");
define("_AMS_AM_LEFT","Links");

define("_AMS_AM_EXPARTS","Abgelaufene Artikel");
define("_AMS_AM_EXPIRED","Abgelaufen");
define("_AMS_AM_CHANGEEXPDATETIME","Ablaufdatum/Zeit �ndern");
define("_AMS_AM_SETEXPDATETIME","Setze Datum/Zeit des Ablaufs");
define("_AMS_AM_NOWSETEXPTIME","Ist jetzt gesetzt auf: %s");

// Added in RC3
define("_AMS_AM_ERRORTOPICNAME", "Sie m�ssen einen Thema Namen eingeben!");
define("_AMS_AM_EMPTYNODELETE", "NIchts zu l�schen!");

// Added 240304 (Mithrandir)
define("_AMS_AM_GROUPPERM", "Berechtigungen");
define("_AMS_AM_SELFILE","W�hle Datei");

// Added Novasmart in 2.42
define("_MULTIPLE_PAGE_GUIDE","Art [pagebreak] zu splitten, um mehrere Seiten");

// Added by Herv�
define("_AMS_AM_UPLOAD_DBERROR_SAVE","Fehler beim anh�ngen der Datei zum Artikel");
define("_AMS_AM_UPLOAD_ERROR","Fehler beim hochladen der Datei");
define("_AMS_AM_UPLOAD_ATTACHFILE","Angeh�ngte Datei(en)");
define("_AMS_AM_APPROVEFORM", "Berechtigung best�tigen");
define("_AMS_AM_SUBMITFORM", "Berechtigung einschicken");
define("_AMS_AM_VIEWFORM", "Berechtigungen ansehen");
define("_AMS_AM_APPROVEFORM_DESC", "W�hle, wer Artikel best�tigen darf");
define("_AMS_AM_SUBMITFORM_DESC", "W�hle, wer Artikel einschicken darf");
define("_AMS_AM_VIEWFORM_DESC", "W�hle, wer welche Themen sehen darf");
define("_AMS_AM_DELETE_SELFILES", "L�sche gew�hlte Datei(en)");
define("_AMS_AM_TOPIC_PICTURE", "Bild hochladen");
define("_AMS_AM_UPLOAD_WARNING", "<B>Warnung, vergessen Sie nicht, Schreibrechte auf folgenden Ordner zu setzen : %s</B>");

define("_AMS_AM_NEWS_UPGRADECOMPLETE", "Upgrade komplett");
define("_AMS_AM_NEWS_UPDATEMODULE", "Aktualisiere Vorlagen und Bl�cke");
define("_AMS_AM_NEWS_UPGRADEFAILED", "Aktualisierung fehlgeschlagen");
define("_AMS_AM_NEWS_UPGRADE", "Upgrade");
define("_AMS_AM_ADD_TOPIC","Ein Thema hinzuf�gen");
define("_AMS_AM_ADD_TOPIC_ERROR","Fehler, Thema existiert bereits!");
define("_AMS_AM_ADD_TOPIC_ERROR1","FEHLER: Kann dieses Thema nicht als �bergeordnetes Thema ausw�hlen!");
define("_AMS_AM_SUB_MENU","Ver�ffentliche dieses Thema als Sub-Men�");
define("_AMS_AM_SUB_MENU_YESNO","Sub-Men�?");
define("_AMS_AM_HITS", "Treffer");
define("_AMS_AM_CREATED", "Erzeugt");
define("_AMS_AM_COMMENTS", "Kommentare");
define("_AMS_AM_VERSION", "Version");
define("_AMS_AM_PUBLISHEDARTICLES", "Ver�ffentlichte Artikel");
define("_AMS_AM_TOPICBANNER", "Banner");
define("_AMS_AM_BANNERINHERIT", "Vererbung von �bergeordneter Instanz");
define("_AMS_AM_RATING", "Bewertung");
define("_AMS_AM_FILTER", "Filter");
define("_AMS_AM_SORTING", "Sortieroptionen");
define("_AMS_AM_SORT", "Sortiere");
define("_AMS_AM_ORDER", "Reihenfolge");
define("_AMS_AM_STATUS", "Status");
define("_AMS_AM_OF", "von");

define("_AMS_AM_MANAGEAUDIENCES", "Verwalte Zielgruppen");
define("_AMS_AM_AUDIENCENAME", "Zielgruppen Name");
define("_AMS_AM_ACCESSRIGHTS", "Zugriffsrechte");
define("_AMS_AM_LINKEDFORUM", "Verkn�pftes Forum");
define("_AMS_AM_VERSIONCOUNT", "Versionen");
define("_AMS_AM_AUDIENCEHASSTORIES", "%u Artikel haben diese Zielgruppe, bitte w�hlen Sie eine  neue Zielgruppe f�r diese Artikel");
define("_AMS_AM_RUSUREDELAUDIENCE", "Sind Sie sicher, das Sie diese Zielgruppen komplett l�schen wollen?");
define("_AMS_AM_PLEASESELECTNEWAUDIENCE", "Bitte w�hlen Sie eine ersatzweise Zielgruppe");
define("_AMS_AM_AUDIENCEDELETED", "Zielgruppe erflgreich gel�scht");
define("_AMS_AM_ERROR_AUDIENCENOTDELETED", "Fehler - Zielgruppe NICHT gel�scht");
define("_AMS_AM_CANNOTDELETEDEFAULTAUDIENCE", "Fehler - Kann die Standard-Zielgruppe nicht l�schen");

define("_AMS_AM_NOTOPICSELECTED", "Kein Thema gew�hlt");
define("_AMS_AM_SUBMIT", "Abschicken");
define("_AMS_AM_ERROR_REORDERERROR", "Fehler - Fehler traten w�hrend der Neuordnung auf");
define("_AMS_AM_REORDERSUCCESSFUL", "Themen neu geordnet");

define("_AMS_AM_NONE", "Kein Bild");
define("_AMS_AM_AUTHOR", "Avatar des Verfassers");

define("_AMS_AM_SPOT_ADD", "Rampenlicht Mini Block hinzuf�gen");
define("_AMS_AM_SPOT_EDITBLOCK", "Bearbeiten der Block Einstellung");
define("_AMS_AM_SPOT_NAME", "Name");
define("_AMS_AM_SPOT_SHOWIMAGE", "Zeige Bild");
define("_AMS_AM_SPOT_SHOWIMAGE_DESC", "W�hle ein Bild zum anzeigen, zeige das Themen Bild oder den Avatar des Verfassers");
define("_AMS_AM_SPOT_WEIGHT", "Gewichtung");
define("_AMS_AM_SPOT_DISPLAY", "Anzeige");
define("_AMS_AM_SPOT_MAIN", "Haupt");
define("_AMS_AM_SPOT_MINI", "Mini");
define("_AMS_AM_SPOTLIGHT", "Rampenlicht");
define("_AMS_AM_WEIGHT", "Gewichtung");
define("_AMS_AM_SPOT_SAVESUCCESS", "Rampenlicht erfolgreich gespeichert");
define("_AMS_AM_SPOT_DELETESUCCESS", "Rampenlicht erfolgreich gel�scht");
define("_AMS_AM_RUSUREDELSPOTLIGHT", "Sind Sie sicher, das sie dieses Rampenlicht entfernen wollen?");

define("_AMS_AM_SPOT_LATESTARTICLE", "Aktuellste Artikel");
define("_AMS_AM_SPOT_LATESTINTOPIC", "Aktuellstes im Thema");
define("_AMS_AM_SPOT_SPECIFICARTICLE", "Spezieller Artikel");
define("_AMS_AM_SPOT_NOIMAGE", "Kein Bild");
define("_AMS_AM_SPOT_MODE_SELECT", "Rampenlicht Modus");
define("_AMS_AM_SPOT_SPECIFYIMAGE", "Spezifiziere Bild");
define("_AMS_AM_SPOT_TOPICIMAGE", "Bild vom Thema");
define("_AMS_AM_SPOT_AUTHORIMAGE", "Avatar des Verfassers");
define("_AMS_AM_SPOT_IMAGE", "Bild");
define("_AMS_AM_SPOT_AUTOTEASER", "Automatischer Anreisser");
define("_AMS_AM_SPOT_MAXLENGTH", "Maximale L�nge des automatischen Anreissers");
define("_AMS_AM_SPOT_TEASER", "Eigener Anreisser Text");
define("_AMS_AM_SPOT_TOPIC_DESC", "Wenn 'Aktuellstes im Thema' gew�hlt ist, welches Thema soll dann gew�hlt werden?");
define("_AMS_AM_SPOT_ARTICLE_DESC", "Wenn 'Spezieller Artikel' gew�hlt ist, welcher Artikel soll angezeigt werden?");
define("_AMS_AM_SPOT_CUSTOM", "Ma�geschneidert");

define("_AMS_AM_PREFERENCES", "Voreinstellungen");
define("_AMS_AM_GOMOD", "Modul starten");
define("_AMS_AM_ABOUT", "�ber");
define("_AMS_AM_MODADMIN", "Modul Administration");
?>