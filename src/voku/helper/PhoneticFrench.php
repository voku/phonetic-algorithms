<?php

declare(strict_types=1);

namespace voku\helper;

/**
 * PhoneticFrench-Helper-Class
 *
 * @package voku\helper
 */
final class PhoneticFrench implements PhoneticInterface
{

  /**
   * Phonetic for the frensh language via "SOUNDEX FR"-algorithm.
   *
   * Édouard BERGÉ © 12.2007 v1.2
   *
   * ---------------------------------------------------------------------------------------------------------
   * Cet algorithme et son application en PHP sont tous deux distribués sous la licence
   *
   * Creative Commons Paternité - Pas d'Utilisation Commerciale 2.0
   * http://creativecommons.org/licenses/by-nc/2.0/fr/
   *
   * Vous êtes libres :
   * - de reproduire, distribuer et communiquer cette création au public
   * - de modifier cette création
   *
   * Selon les conditions suivantes :
   * - Paternité. Vous devez citer le nom de l'auteur original de la manière indiquée
   * par l'auteur de l'oeuvre ou le titulaire des droits qui vous confère cette
   * autorisation (mais pas d'une manière qui suggérerait qu'ils vous soutiennent
   * ou approuvent votre utilisation de l'oeuvre).
   * Demande de l'auteur: Citer "Édouard BERGÉ" et/ou " http://www.roudoudou.com"
   * - Pas d'Utilisation Commerciale. Vous n'avez pas le droit d'utiliser cette
   * création à des fins commerciales sauf autorisation de l'auteur.
   * ---------------------------------------------------------------------------------------------------------
   *
   * Pourquoi un nouveau Soundex? Il en existe déjà beaucoup de variantes non? Oui mais...
   *
   * - Ils généralisent trop les sons, provocants beaucoup de faux positifs.
   * - Leur conversion phonétique est limitée alors que le français est complexe, résultant beaucoup de manqués.
   *
   * L'objectif est donc clair. Je veux...
   *
   * - Moins de faux positifs.
   * - Plus de réussite.
   * - De plus, je trouve pratique d'avoir une conversion qui reste "lisible".
   * - Une certaine correction des erreurs orthographiques les plus courantes.
   *
   * Commençant mes développements avec 200 mots de référence, relire cette liste à chaque fois était
   * ridiculement improductif et source d'erreurs. Je suis alors passé à la vitesse supérieure en me
   *
   * constituant un corpus de test de 7.000 mots (choisis en "feuilletant" un dictionnaire), ainsi
   * qu'une page de test pour automatiser les vérifications et la détection d'éventuelles régressions.
   *
   * Pour terminer la validation de mon algorithme, j'ai téléchargé une version française du
   * dictionnaire myspell pour l'ajouter à ma page de test. Cette ultime vérification me permit
   * de corriger quelques imperfections. Au final, cet algorithme a été testé avec plus de 70.000 mots.
   *
   * Attention, il n'est pas parfait!  Mais j'ai enfin eu ce que je voulais, avec très peu de concessions,
   * à savoir un algorithme qui se comporte bien avec des mots français. L'occasion pour moi de remercier
   * quelques personnes:
   *
   * - Frédéric Brouard pour son article sur les soundex.
   * http://sqlpro.developpez.com/cours/soundex/
   *
   * - Florent Bruneau de qui j'ai repris quelques morceaux de code. Lui même inspiré par Frédéric Brouard.
   * http://blog.mymind.fr/post/2007/03/15/Soundex-Francais
   *
   * - Christophe Pythoud et Vazkor (Jean-Claude M.) pour leur immense travail sur le dictionnaire myspell.
   * http://perso.latribu.com/rocky2/index.html (Le chien n'est pas méchant)
   *
   *
   * Une nouvelle version?
   *
   * Je n'ai pas encore d'axe de travail. Soit je me sers de mon corpus de test pour compacter le code produit
   * tout en gardant l'unicité de sens, soit j'utiliserai un dictionnaire. Mais on n'en est pas encore là.
   *
   * Édouard BERGÉ
   *
   * @param $word
   *
   * @return string
   */
  public function phonetic_word($word): string
  {
    $accents = [
        'É' => 'E',
        'È' => 'E',
        'Ë' => 'E',
        'Ê' => 'E',
        'Á' => 'A',
        'À' => 'A',
        'Ä' => 'A',
        'Â' => 'A',
        'Å' => 'A',
        'Ã' => 'A',
        'Æ' => 'E',
        'Ï' => 'I',
        'Î' => 'I',
        'Ì' => 'I',
        'Í' => 'I',
        'Ô' => 'O',
        'Ö' => 'O',
        'Ò' => 'O',
        'Ó' => 'O',
        'Õ' => 'O',
        'Ø' => 'O',
        'Œ' => 'OEU',
        'Ú' => 'U',
        'Ù' => 'U',
        'Û' => 'U',
        'Ü' => 'U',
        'Ñ' => 'N',
        'Ç' => 'S',
        '¿' => 'E',
    ];

    $min2maj = [
        'é' => 'É',
        'è' => 'È',
        'ë' => 'Ë',
        'ê' => 'Ê',
        'á' => 'Á',
        'â' => 'Â',
        'à' => 'À',
        'Ä' => 'A',
        'Â' => 'A',
        'å' => 'Å',
        'ã' => 'Ã',
        'æ' => 'Æ',
        'ï' => 'Ï',
        'î' => 'Î',
        'ì' => 'Ì',
        'í' => 'Í',
        'ô' => 'Ô',
        'ö' => 'Ö',
        'ò' => 'Ò',
        'ó' => 'Ó',
        'õ' => 'Õ',
        'ø' => 'Ø',
        'œ' => 'Œ',
        'ú' => 'Ú',
        'ù' => 'Ù',
        'û' => 'Û',
        'ü' => 'Ü',
        'ç' => 'Ç',
        'ñ' => 'Ñ',
        'ß' => 'S',
    ];
    $word = \strtr($word, $min2maj); // minuscules accentuées ou composées en majuscules simples
    $word = \strtr($word, $accents); // majuscules accentuées ou composées en majuscules simples

    $word = UTF8::to_ascii($word);

    $word = \strtoupper($word); // on passe tout le reste en majuscules

    $word = \preg_replace('`[^A-Z]`', '', $word); // on garde uniquement les lettres de A à Z

    $sBack = $word; // on sauve le code (utilisé pour les mots très courts)

    $word = \preg_replace('`O[O]+`', 'OU', $word);       // pré traitement OO... -> OU
    $word = \str_replace('SAOU', 'SOU', $word);              // pré traitement SAOU -> SOU
    $word = \str_replace('OES', 'OS', $word);                // pré traitement OES -> OS
    $word = \str_replace('CCH', 'K', $word);                 // pré traitement CCH -> K
    $word = \preg_replace('`CC([IYE])`', 'KS$1', $word); // CCI CCY CCE
    $word = \preg_replace('`(.)\1`', '$1', $word);       // supression des répétitions

    // quelques cas particuliers
    if ($word == "CD") {
      return $word;
    }

    if ($word == "BD") {
      return $word;
    }

    if ($word == "BV") {
      return $word;
    }

    if ($word == "TABAC") {
      return "TABA";
    }

    if ($word == "FEU") {
      return "FE";
    }

    if ($word == "FE") {
      return $word;
    }

    if ($word == "FER") {
      return $word;
    }

    if ($word == "FIEF") {
      return $word;
    }

    if ($word == "FJORD") {
      return $word;
    }

    if ($word == "GOAL") {
      return "GOL";
    }

    if ($word == "FLEAU") {
      return "FLEO";
    }

    if ($word == "HIER") {
      return "IER";
    }

    if ($word == "HEU") {
      return "E";
    }

    if ($word == "HE") {
      return "E";
    }

    if ($word == "OS") {
      return $word;
    }

    if ($word == "RIZ") {
      return "RI";
    }

    if ($word == "RAZ") {
      return "RA";
    }

    // pré-traitements
    $word = \preg_replace('`OIN[GT]$`', 'OIN', $word);                      // terminaisons OING -> OIN
    $word = \preg_replace('`E[RS]$`', 'E', $word);                          // supression des terminaisons infinitifs et participes pluriels
    $word = \preg_replace('`(C|CH)OEU`', 'KE', $word);                      // pré traitement OEU -> EU
    $word = \str_replace('MOEU', 'ME', $word);                                  // pré traitement OEU -> EU
    $word = \preg_replace('`OE([UI]+)([BCDFGHJKLMNPQRSTVWXZ])`', 'E$1$2', $word); // pré traitement OEU OEI -> E
    $word = \preg_replace('`^GEN[TS]$`', 'JAN', $word);                     // pré traitement GEN -> JAN
    $word = \str_replace('CUEI', 'KEI', $word);                                 // pré traitement accueil
    $word = \preg_replace('`([^AEIOUYC])AE([BCDFGHJKLMNPQRSTVWXZ])`', '$1E$2', $word);  // pré traitement AE -> E
    $word = \preg_replace('`AE([QS])`', 'E$1', $word);                      // pré traitement AE -> E
    $word = \preg_replace('`AIE([BCDFGJKLMNPQRSTVWXZ])`', 'AI$1', $word);   // pré-traitement AIE(consonne) -> AI
    $word = \str_replace('ANIEM', 'ANIM', $word);                               // pré traitement NIEM -> NIM
    $word = \preg_replace('`(DRA|TRO|IRO)P$`', '$1', $word);                // P terminal muet
    $word = \preg_replace('`(LOM)B$`', '$1', $word);                        // B terminal muet
    $word = \preg_replace('`(RON|POR)C$`', '$1', $word);                    // C terminal muet
    $word = \preg_replace('`PECT$`', 'PET', $word);                         // C terminal muet
    $word = \preg_replace('`ECUL$`', 'CU', $word);                          // L terminal muet
    $word = \preg_replace('`(CHA|CA|E)M(P|PS)$`', '$1N', $word);            // P ou PS terminal muet
    $word = \preg_replace('`(TAN|RAN)G$`', '$1', $word);                    // G terminal muet


    // sons YEUX
    $word = \preg_replace('`([^VO])ILAG`', '$1IAJ', $word);
    $word = \preg_replace('`([^TRH])UIL(AR|E)(.+)`', '$1UI$2$3', $word);
    $word = \preg_replace('`([G])UIL([AEO])`', '$1UI$2', $word);
    $word = \preg_replace('`([NSPM])AIL([AEO])`', '$1AI$2', $word);

    $convMIn = [
        "DILAI",
        "DILON",
        "DILER",
        "DILEM",
        "RILON",
        "TAILE",
        "GAILET",
        "AILAI",
        "AILAR",
        "OUILA",
        "EILAI",
        "EILAR",
        "EILER",
        "EILEM",
        "REILET",
        "EILET",
        "AILOL",
    ];
    $convMOut = [
        "DIAI",
        "DION",
        "DIER",
        "DIEM",
        "RION",
        "TAIE",
        "GAIET",
        "AIAI",
        "AIAR",
        "OUIA",
        "AIAI",
        "AIAR",
        "AIER",
        "AIEM",
        "RAIET",
        "EIET",
        "AIOL",
    ];
    $word = \str_replace($convMIn, $convMOut, $word);
    $word = \preg_replace('`([^AEIOUY])(SC|S)IEM([EA])`', '$1$2IAM$3', $word);  // IEM -> IAM
    $word = \preg_replace('`^(SC|S)IEM([EA])`', '$1IAM$2', $word);              // IEM -> IAM

    // MP MB -> NP NB
    $convMIn = ['OMB', 'AMB', 'OMP', 'AMP', 'IMB', 'EMP', 'GEMB', 'EMB', 'UMBL', 'CIEN'];
    $convMOut = ['ONB', 'ANB', 'ONP', 'ANP', 'INB', 'ANP', 'JANB', 'ANB', 'INBL', 'SIAN'];
    $word = \str_replace($convMIn, $convMOut, $word);

    // Sons en K
    $word = \preg_replace('`^ECHO$`', 'EKO', $word);    // cas particulier écho
    $word = \preg_replace('`^ECEUR`', 'EKEUR', $word);  // cas particulier écœuré
    // Choléra Chœur mais pas chocolat!
    $word = \preg_replace('`^CH(OG+|OL+|OR+|EU+|ARIS|M+|IRO|ONDR)`', 'K$1', $word);              //En début de mot
    $word = \preg_replace('`(YN|RI)CH(OG+|OL+|OC+|OP+|OM+|ARIS|M+|IRO|ONDR)`', '$1K$2', $word);  //Ou devant une consonne
    $word = \str_replace('CHS', 'CH', $word);
    $word = \preg_replace('`CH(AIQ)`', 'K$1', $word);
    $word = \preg_replace('`^ECHO([^UIPY])`', 'EKO$1', $word);
    $word = \preg_replace('`ISCH(I|E)`', 'ISK$1', $word);
    $word = \preg_replace('`^ICHT`', 'IKT', $word);
    $word = \str_replace('ORCHID', 'ORKID', $word);
    $word = \str_replace('ONCHIO', 'ONKIO', $word);
    $word = \str_replace('ACHIA', 'AKIA', $word);                 // retouche ACHIA -> AKIA
    $word = \preg_replace('`([^C])ANICH`', '$1ANIK', $word);  // ANICH -> ANIK 	1/2
    $word = \str_replace('OMANIK', 'OMANICH', $word);             // cas particulier 	2/2
    $word = \preg_replace('`ACHY([^D])`', 'AKI$1', $word);
    $word = \preg_replace('`([AEIOU])C([BDFGJKLMNPQRTVWXZ])`', '$1K$2', $word); // voyelle, C, consonne sauf H

    $convPrIn = [
        'EUCHA',
        'YCHIA',
        'YCHA',
        'YCHO',
        'YCHED',
        'ACHEO',
        'RCHEO',
        'RCHES',
        'ECHN',
        'OCHTO',
        'CHORA',
        'CHONDR',
        'CHORE',
        'MACHM',
        'BRONCHO',
        'LICHOS',
        'LICHOC',
    ];
    $convPrOut = [
        'EKA',
        'IKIA',
        'IKA',
        'IKO',
        'IKED',
        'AKEO',
        'RKEO',
        'RKES',
        'EKN',
        'OKTO',
        'KORA',
        'KONDR',
        'KORE',
        'MAKM',
        'BRONKO',
        'LIKOS',
        'LIKOC',
    ];
    $word = \str_replace($convPrIn, $convPrOut, $word);

    // Weuh (perfectible)
    $convPrIn = ['WA', 'WO', 'WI', 'WHI', 'WHY', 'WHA', 'WHO'];
    $convPrOut = ['OI', 'O', 'OUI', 'OUI', 'OUI', 'OUA', 'OU'];
    $word = \str_replace($convPrIn, $convPrOut, $word);

    // Gueu, Gneu, Jeu et quelques autres
    $convPrIn = [
        'GNES',
        'GNET',
        'GNER',
        'GNE',
        'GI',
        'GNI',
        'GNA',
        'GNOU',
        'GNUR',
        'GY',
        'OUGAIN',
        'AGEOL',
        'AGEOT',
        'GEOLO',
        'GEOM',
        'GEOP',
        'GEOG',
        'GEOS',
        'GEORG',
        'GEOR',
        'NGEOT',
        'UGEOT',
        'GEOT',
        'GEOD',
        'GEOC',
        'GEO',
        'GEA',
        'GE',
        'QU',
        'Q',
        'CY',
        'CI',
        'CN',
        'ICM',
        'CEAT',
        'CE',
        'CR',
        'CO',
        'CUEI',
        'CU',
        'VENCA',
        'CA',
        'CS',
        'CLEN',
        'CL',
        'CZ',
        'CTIQ',
        'CTIF',
        'CTIC',
        'CTIS',
        'CTIL',
        'CTIO',
        'CTI',
        'CTU',
        'CTE',
        'CTO',
        'CTR',
        'CT',
        'PH',
        'TH',
        'OW',
        'LH',
        'RDL',
        'CHLO',
        'CHR',
        'PTIA',
    ];
    $convPrOut = [
        'NIES',
        'NIET',
        'NIER',
        'NE',
        'JI',
        'NI',
        'NIA',
        'NIOU',
        'NIUR',
        'JI',
        'OUGIN',
        'AJOL',
        'AJOT',
        'JEOLO',
        'JEOM',
        'JEOP',
        'JEOG',
        'JEOS',
        'JORJ',
        'JEOR',
        'NJOT',
        'UJOT',
        'JEOT',
        'JEOD',
        'JEOC',
        'JO',
        'JA',
        'JE',
        'K',
        'K',
        'SI',
        'SI',
        'KN',
        'IKM',
        'SAT',
        'SE',
        'KR',
        'KO',
        'KEI',
        'KU',
        'VANSA',
        'KA',
        'KS',
        'KLAN',
        'KL',
        'KZ',
        'KTIK',
        'KTIF',
        'KTIS',
        'KTIS',
        'KTIL',
        'KSIO',
        'KTI',
        'KTU',
        'KTE',
        'KTO',
        'KTR',
        'KT',
        'F',
        'T',
        'OU',
        'L',
        'RL',
        'KLO',
        'KR',
        'PSIA',
    ];
    $word = \str_replace($convPrIn, $convPrOut, $word);

    $word = \preg_replace('`GU([^RLMBSTPZN])`', 'G$1', $word); // Gueu !
    $word = \preg_replace('`GNO([MLTNRKG])`', 'NIO$1', $word); // GNO ! Tout sauf S pour gnos
    $word = \preg_replace('`GNO([MLTNRKG])`', 'NIO$1', $word); // bis -> gnognotte! Si quelqu'un sait le faire en une seule regexp...

    // TI -> SI v2.0
    $convPrIn = [
        'BUTIE',
        'BUTIA',
        'BATIA',
        'ANTIEL',
        'RETION',
        'ENTIEL',
        'ENTIAL',
        'ENTIO',
        'ENTIAI',
        'UJETION',
        'ATIEM',
        'PETIEN',
        'CETIE',
        'OFETIE',
        'IPETI',
        'LBUTION',
        'BLUTION',
        'LETION',
        'LATION',
        'SATIET',
    ];
    $convPrOut = [
        'BUSIE',
        'BUSIA',
        'BASIA',
        'ANSIEL',
        'RESION',
        'ENSIEL',
        'ENSIAL',
        'ENSIO',
        'ENSIAI',
        'UJESION',
        'ASIAM',
        'PESIEN',
        'CESIE',
        'OFESIE',
        'IPESI',
        'LBUSION',
        'BLUSION',
        'LESION',
        'LASION',
        'SASIET',
    ];
    $word = \str_replace($convPrIn, $convPrOut, $word);
    $word = \preg_replace('`(.+)ANTI(AL|O)`', '$1ANSI$2', $word);   // sauf antialcoolique, antialbumine, antialarmer, ...
    $word = \preg_replace('`(.+)INUTI([^V])`', '$1INUSI$2', $word); // sauf inutilité, inutilement, diminutive, ...
    $word = \preg_replace('`([^O])UTIEN`', '$1USIEN', $word);       // sauf soutien, ...
    $word = \preg_replace('`([^DE])RATI[E]$`', '$1RASI$2', $word);  // sauf xxxxxcratique, ...
    // TIEN TION -> SIEN SION v3.1
    $word = \preg_replace('`([^SNEU]|KU|KO|RU|LU|BU|TU|AU)T(IEN|ION)`', '$1S$2', $word);


    // H muet
    $word = \preg_replace('`([^CS])H`', '$1', $word);  // H muet
    $word = \str_replace("ESH", "ES", $word);      // H muet
    $word = \str_replace("NSH", "NS", $word);      // H muet
    $word = \str_replace("SH", "CH", $word);       // ou pas!

    // NASALES
    $convNasIn = [
        'OMT',
        'IMB',
        'IMP',
        'UMD',
        'TIENT',
        'RIENT',
        'DIENT',
        'IEN',
        'YMU',
        'YMO',
        'YMA',
        'YME',
        'YMI',
        'YMN',
        'YM',
        'AHO',
        'FAIM',
        'DAIM',
        'SAIM',
        'EIN',
        'AINS',
    ];
    $convNasOut = [
        'ONT',
        'INB',
        'INP',
        'OND',
        'TIANT',
        'RIANT',
        'DIANT',
        'IN',
        'IMU',
        'IMO',
        'IMA',
        'IME',
        'IMI',
        'IMN',
        'IN',
        'AO',
        'FIN',
        'DIN',
        'SIN',
        'AIN',
        'INS',
    ];
    $word = \str_replace($convNasIn, $convNasOut, $word);
    // AIN -> IN v2.0
    $word = \preg_replace('`AIN$`', 'IN', $word);
    $word = \preg_replace('`AIN([BTDK])`', 'IN$1', $word);
    // UN -> IN
    $word = \preg_replace('`([^O])UND`', '$1IND', $word); // aucun mot français ne commence par UND!
    $word = \preg_replace('`([JTVLFMRPSBD])UN([^IAE])`', '$1IN$2', $word);
    $word = \preg_replace('`([JTVLFMRPSBD])UN$`', '$1IN', $word);
    $word = \preg_replace('`RFUM$`', 'RFIN', $word);
    $word = \str_replace('LUMB', 'LINB', $word);
    // EN -> AN
    $word = \preg_replace('`([^BCDFGHJKLMNPQRSTVWXZ])EN`', '$1AN', $word);
    $word = \preg_replace('`([VTLJMRPDSBFKNG])EN([BRCTDKZSVN])`', '$1AN$2', $word); // deux fois pour les motifs recouvrants malentendu, pendentif, ...
    $word = \preg_replace('`([VTLJMRPDSBFKNG])EN([BRCTDKZSVN])`', '$1AN$2', $word); // si quelqu'un sait faire avec une seule regexp!
    $word = \preg_replace('`^EN([BCDFGHJKLNPQRSTVXZ]|CH|IV|ORG|OB|UI|UA|UY)`', 'AN$1', $word);
    $word = \preg_replace('`(^[JRVTH])EN([DRTFGSVJMP])`', '$1AN$2', $word);
    $word = \preg_replace('`SEN([ST])`', 'SAN$1', $word);
    $word = \preg_replace('`^DESENIV`', 'DESANIV', $word);
    $word = \preg_replace('`([^M])EN(UI)`', '$1AN$2', $word);
    $word = \preg_replace('`(.+[JTVLFMRPSBD])EN([JLFDSTG])`', '$1AN$2', $word);
    // EI -> AI
    $word = \preg_replace('`([VSBSTNRLPM])E[IY]([ACDFRJLGZ])`', '$1AI$2', $word);

    // Histoire d'Ô
    $convNasIn = ['EAU', 'EU', 'Y', 'EOI', 'JEA', 'OIEM', 'OUANJ', 'OUA', 'OUENJ'];
    $convNasOut = ['O', 'E', 'I', 'OI', 'JA', 'OIM', 'OUENJ', 'OI', 'OUANJ'];
    $word = \str_replace($convNasIn, $convNasOut, $word);
    $word = \preg_replace('`AU([^E])`', 'O$1', $word); // AU sans E qui suit

    // Les retouches!
    $word = \preg_replace('`^BENJ`', 'BINJ', $word);        // retouche BENJ -> BINJ
    $word = \str_replace('RTIEL', 'RSIEL', $word);              // retouche RTIEL -> RSIEL
    $word = \str_replace('PINK', 'PONK', $word);                // retouche PINK -> PONK
    $word = \str_replace('KIND', 'KOND', $word);                // retouche KIND -> KOND
    $word = \preg_replace('`KUM(N|P)`', 'KON$1', $word);    // retouche KUMN KUMP
    $word = \str_replace('LKOU', 'LKO', $word);                 // retouche LKOU -> LKO
    $word = \str_replace('EDBE', 'EBE', $word);                 // retouche EDBE pied-bœuf
    $word = \str_replace('ARCM', 'ARKM', $word);                // retouche SCH -> CH
    $word = \str_replace('SCH', 'CH', $word);                   // retouche SCH -> CH
    $word = \preg_replace('`^OINI`', 'ONI', $word);         // retouche début OINI -> ONI
    $word = \preg_replace('`([^NDCGRHKO])APT`', '$1AT', $word);  // retouche APT -> AT
    $word = \preg_replace('`([L]|KON)PT`', '$1T', $word);   // retouche LPT -> LT
    $word = \str_replace('OTB', 'OB', $word);                   // retouche OTB -> OB (hautbois)
    $word = \str_replace('IXA', 'ISA', $word);                  // retouche IXA -> ISA
    $word = \str_replace('TG', 'G', $word);                     // retouche TG -> G
    $word = \preg_replace('`^TZ`', 'TS', $word);            // retouche début TZ -> TS
    $word = \str_replace('PTIE', 'TIE', $word);                 // retouche PTIE -> TIE
    $word = \str_replace('GT', 'T', $word);                     // retouche GT -> T
    $word = \str_replace("ANKIEM", "ANKILEM", $word);           // retouche tranquillement
    $word = \preg_replace("`(LO|RE)KEMAN`", "$1KAMAN", $word);  // KEMAN -> KAMAN
    $word = \preg_replace('`NT(B|M)`', 'N$1', $word);       // retouche TB -> B  TM -> M
    $word = \str_replace('GSU', 'SU', $word);                   // retouche GS -> SU
    $word = \str_replace('ESD', 'ED', $word);                   // retouche ESD -> ED
    $word = \str_replace('LESKEL', 'LEKEL', $word);             // retouche LESQUEL -> LEKEL
    $word = \str_replace('CK', 'K', $word);                     // retouche CK -> K

    // Terminaisons
    $word = \preg_replace('`USIL$`', 'USI', $word);         // terminaisons USIL -> USI
    $word = \preg_replace('`X$|[TD]S$|[DS]$`', '', $word);  // terminaisons TS DS LS X T D S...  v2.0
    $word = \preg_replace('`([^KL]+)T$`', '$1', $word);     // sauf KT LT terminal
    $word = \preg_replace('`^[H]`', '', $word);             // H pseudo muet en début de mot, je sais, ce n'est pas une terminaison

    $sBack2 = $word; // on sauve le code (utilisé pour les mots très courts)

    $word = \preg_replace('`TIL$`', 'TI', $word);           // terminaisons TIL -> TI
    $word = \preg_replace('`LC$`', 'LK', $word);            // terminaisons LC -> LK
    $word = \preg_replace('`L[E]?[S]?$`', 'L', $word);      // terminaisons LE LES -> L
    $word = \preg_replace('`(.+)N[E]?[S]?$`', '$1N', $word); // terminaisons NE NES -> N
    $word = \preg_replace('`EZ$`', 'E', $word);             // terminaisons EZ -> E
    $word = \preg_replace('`OIG$`', 'OI', $word);           // terminaisons OIG -> OI
    $word = \preg_replace('`OUP$`', 'OU', $word);           // terminaisons OUP -> OU
    $word = \preg_replace('`([^R])OM$`', '$1ON', $word);    // terminaisons OM -> ON sauf ROM
    $word = \preg_replace('`LOP$`', 'LO', $word);           // terminaisons LOP -> LO
    $word = \preg_replace('`NTANP$`', 'NTAN', $word);       // terminaisons NTANP -> NTAN
    $word = \preg_replace('`TUN$`', 'TIN', $word);          // terminaisons TUN -> TIN
    $word = \preg_replace('`AU$`', 'O', $word);             // terminaisons AU -> O
    $word = \preg_replace('`EI$`', 'AI', $word);            // terminaisons EI -> AI
    $word = \preg_replace('`R[DG]$`', 'R', $word);          // terminaisons RD RG -> R
    $word = \preg_replace('`ANC$`', 'AN', $word);           // terminaisons ANC -> AN
    $word = \preg_replace('`KROC$`', 'KRO', $word);         // terminaisons C muet de CROC, ESCROC
    $word = \preg_replace('`HOUC$`', 'HOU', $word);         // terminaisons C muet de CAOUTCHOUC
    $word = \preg_replace('`OMAC$`', 'OMA', $word);         // terminaisons C muet de ESTOMAC (mais pas HAMAC)
    $word = \preg_replace('`([J])O([NU])[CG]$`', '$1O$2', $word);// terminaisons C et G muet de OUC ONC OUG
    $word = \preg_replace('`([^GTR])([AO])NG$`', '$1$2N', $word);// terminaisons G muet ANG ONG sauf GANG GONG TANG TONG
    $word = \preg_replace('`UC$`', 'UK', $word);            // terminaisons UC -> UK
    $word = \preg_replace('`AING$`', 'IN', $word);          // terminaisons AING -> IN
    $word = \preg_replace('`([EISOARN])C$`', '$1K', $word); // terminaisons C -> K
    $word = \preg_replace('`([ABD-MO-Z]+)[EH]+$`', '$1', $word);  // terminaisons E ou H sauf pour C et N
    $word = \preg_replace('`EN$`', 'AN', $word);            // terminaisons EN -> AN (difficile à faire avant sans avoir des soucis) Et encore, c'est pas top!
    $word = \preg_replace('`(NJ)EN$`', '$1AN', $word);      // terminaisons EN -> AN
    $word = \preg_replace('`^PAIEM`', 'PAIM', $word);       // PAIE -> PAI
    $word = \preg_replace('`([^NTB])EF$`', '\1', $word);    // F muet en fin de mot

    $word = \preg_replace('`(.)\1`', '$1', $word);          // supression des répétitions (suite à certains remplacements)

    // cas particuliers, bah au final, je n'en ai qu'un ici
    $convPartIn = ['FUEL'];
    $convPartOut = ['FIOUL'];
    $word = \str_replace($convPartIn, $convPartOut, $word);

    // Ce sera le seul code retourné à une seule lettre!
    if ($word == 'O') {
      return $word;
    }

    // seconde chance sur les mots courts qui ont souffert de la simplification
    if (\strlen($word) < 2) {
      // Sigles ou abréviations
      if (\preg_match("`[BCDFGHJKLMNPQRSTVWXYZ]*`", $sBack)) {
        return $sBack;
      }

      if (\preg_match("`[RFMLVSPJDF][AEIOU]`", $sBack)) {
        // mots de trois lettres supposés simples
        if (\strlen($sBack) == 3) {
          return \substr($sBack, 0, 2);
        }

        // mots de quatre lettres supposés simples
        if (\strlen($sBack) == 4) {
          return \substr($sBack, 0, 3);
        }
      }

      if (\strlen($sBack2) > 1) {
        return $sBack2;
      }
    }

    // Je limite à 16 caractères mais vous faites comme vous voulez!
    if (\strlen($word) > 1) {
      return \substr($word, 0, 16);
    }

    return '';
  }
}
