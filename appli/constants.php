<?php
DEFINE('APP_VERSION', '3.0');

// Niveaux de logs
DEFINE('LOG_LEVEL_DEBUG', 0);
DEFINE('LOG_LEVEL_INFO', 1);
DEFINE('LOG_LEVEL_WARN', 2);
DEFINE('LOG_LEVEL_HACK', 3);
DEFINE('LOG_LEVEL_ERR', 4);
DEFINE('LOG_LEVEL_PHP', 10);

// Niveaux d'authentification
DEFINE('AUTH_LEVEL_NONE', 0);
DEFINE('AUTH_LEVEL_USER', 1);
DEFINE('AUTH_LEVEL_ADMIN', 2);
DEFINE('AUTH_LEVEL_SUPERADMIN', 3);

// Librairies JS
DEFINE('JS_PHOTO', 'photo');
DEFINE('JS_GROWLER', 'growler');
DEFINE('JS_MODAL', 'modal');
DEFINE('JS_SCROLL_REFRESH', 'scroll_refresh');
DEFINE('JS_TASTE', 'taste');
DEFINE('JS_ARTICLE', 'article');
DEFINE('JS_COVOIT', 'covoit');
DEFINE('JS_AUTOCOMPLETE', 'autocomplete');
DEFINE('JS_FORUM', 'forum');
DEFINE('JS_DATEPICKER', 'datepicker');
DEFINE('JS_SEARCH', 'search');
DEFINE('JS_ANNONCE', 'annonce');

// Type de news
DEFINE('NEWS_TYPE_ARTICLE', 1);

// Dimensions des photos
DEFINE("MAX_SIZE", 1000);
DEFINE("MAX_DIMENSION", 1000);

DEFINE("ONLINE_TIME_LIMIT", 10000);

// Photo type
DEFINE('PHOTO_TYPE_USER', 0);
DEFINE('PHOTO_TYPE_ARTICLE', 1);
DEFINE('PHOTO_TYPE_ANNONCE', 2);

// Search types
DEFINE('SEARCH_TYPE_USER', 'user');
DEFINE('SEARCH_TYPE_CONCERT', 'concert');
DEFINE('SEARCH_TYPE_ARTICLE', 'article');
DEFINE('SEARCH_TYPE_COVOIT', 'covoit');
DEFINE('SEARCH_TYPE_ANNONCE', 'annonce');

// REFRESH RESULTS
DEFINE('NB_SEARCH_RESULTS', 21);
DEFINE('NB_MAILBOX_RESULTS', 40);
DEFINE('NB_MESSAGE_RESULTS', 10);

// Growler
DEFINE('GROWLER_INFO', 'info');
DEFINE('GROWLER_ERR', 'err');
DEFINE('GROWLER_OK', 'ok');

// Messages Validation
DEFINE('MSG_BLOCK_OK', 201);
DEFINE('MESSAGE_201', "L\'utilisateur a été bloqué.");
DEFINE('MSG_VALIDATION_SENT', 210);
DEFINE('MESSAGE_210', "Un mail vous a été envoyé pour modifier votre mot de passe.");
DEFINE('MSG_VALIDATION_SUCCESS', 211);
DEFINE('MESSAGE_211', "Votre compte a été validé, vous pouvez à présent vous connecter.");
DEFINE('MSG_ACCOUNT_DELETED', 212);
DEFINE('MESSAGE_212', "Votre compte a été supprimé.");
DEFINE('MSG_VALIDATION_PWD', 213);
DEFINE('MESSAGE_213', "Votre mot de passe a été modifié.");
DEFINE('MSG_ARTICLE_DELETE_SUCCESS', 221);
DEFINE('MESSAGE_221', "L'article a été supprimé.");
DEFINE('MSG_PWD_SENT', 222);
DEFINE('MESSAGE_222', "Vos identifiants ont étés envoyés par mail.");
DEFINE('MSG_ADM_SWITCH', 250);
DEFINE('MESSAGE_250', "Vous avez changé votre utilisateur courant.");
DEFINE('MSG_SENT_OK', 260);
DEFINE('MESSAGE_260', "Message envoyé.");
DEFINE('MSG_COVOIT_OK', 261);
DEFINE('MESSAGE_261', "Covoiturage enregistré.");
DEFINE('MSG_ANNONCE_OK', 262);
DEFINE('MESSAGE_262', "Annonce enregistrée.");

// Messages d'erreurs
DEFINE('ERR_DEFAULT', 400);
DEFINE('MESSAGE_400', 'Une erreur est survenue.');
DEFINE('ERR_AUTH', 401);
DEFINE('MESSAGE_401', 'Authentification requise.');
DEFINE('ERR_LOGIN', 402);
DEFINE('MESSAGE_402', 'Mauvais login / mot de passe.');
DEFINE('ERR_MAIL_NOT_VALIDATED', 403);
DEFINE('MESSAGE_403', "Votre email n\'a pas été validé, vous devez cliquer sur le lien qui vous a été envoyé par email.");
DEFINE('ERR_VALIDATION_FAILURE', 405);
DEFINE('MESSAGE_405', "La validation a échouée, merci de réessayer plus tard.");
DEFINE('ERR_CONTENT', 410);
DEFINE('MESSAGE_410', 'Contenu introuvable.');
DEFINE('ERR_MAIL', 420);
DEFINE('MESSAGE_420', "Le message n\'a pas pu être envoyé, merci de réessayer.");
DEFINE('ERR_SELLER', 430);
DEFINE('MESSAGE_430', "Le vendeur a quitté le site.");
DEFINE('ERR_BLACKLISTED', 450);
DEFINE('MESSAGE_450', "Cet utilisateur a quitté le site.");

// CODE ERREURS
DEFINE('ERROR_SQL', 501);
DEFINE('MESSAGE_501', "Un erreur est survenue.");
DEFINE('ERROR_BEHAVIOR', 502);
DEFINE('MESSAGE_502', "Un erreur est survenue.");
DEFINE('ERROR_NOT_FOUND', 504);
DEFINE('MESSAGE_504', "Contenu introuvable.");
// Status de link
DEFINE('LINK_STATUS_NONE', 0);
DEFINE('LINK_STATUS_RECEIVED', 5);
DEFINE('LINK_STATUS_SENT', 6);
DEFINE('LINK_STATUS_ACCEPTED', 7);
DEFINE('LINK_STATUS_BLACKLIST', 8);
DEFINE('LINK_STATUS_BLACKLISTED', 9);


//Status de mail
DEFINE('MESSAGE_STATUS_SENT', 1);
DEFINE('MESSAGE_STATUS_READ', 2);
DEFINE('MESSAGE_STATUS_DELETED', 4);
DEFINE('MESSAGE_STATUS_ADMIN', 9);

// code retour JSON
DEFINE('JSON_OK', 200);
DEFINE('JSON_ERR', 500);

// tastes
DEFINE('TASTE_TYPE_BAND', 1);
DEFINE('TASTE_TYPE_SERIE', 2);
DEFINE('TASTE_TYPE_BOOK', 3);
DEFINE('TASTE_TYPE_FILM', 4);
DEFINE('TASTE_TYPE_PASSION', 5);

// Article
DEFINE('ARTICLE_STATE_ON_SALE', 1);

// Geoloc
DEFINE('COEF_DISTANCE', 0.02);