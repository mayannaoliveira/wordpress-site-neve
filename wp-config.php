<?php
/**
 * As configurações básicas do WordPress
 *
 * O script de criação wp-config.php usa esse arquivo durante a instalação.
 * Você não precisa usar o site, você pode copiar este arquivo
 * para "wp-config.php" e preencher os valores.
 *
 * Este arquivo contém as seguintes configurações:
 *
 * * Configurações do MySQL
 * * Chaves secretas
 * * Prefixo do banco de dados
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Configurações do MySQL - Você pode pegar estas informações com o serviço de hospedagem ** //
/** O nome do banco de dados do WordPress */
define( 'DB_NAME', 'wordpress' );

/** Usuário do banco de dados MySQL */
define( 'DB_USER', 'root' );

/** Senha do banco de dados MySQL */
define( 'DB_PASSWORD', '' );

/** Nome do host do MySQL */
define( 'DB_HOST', 'localhost' );

/** Charset do banco de dados a ser usado na criação das tabelas. */
define( 'DB_CHARSET', 'utf8mb4' );

/** O tipo de Collate do banco de dados. Não altere isso se tiver dúvidas. */
define( 'DB_COLLATE', '' );

/**#@+
 * Chaves únicas de autenticação e salts.
 *
 * Altere cada chave para um frase única!
 * Você pode gerá-las
 * usando o {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org
 * secret-key service}
 * Você pode alterá-las a qualquer momento para invalidar quaisquer
 * cookies existentes. Isto irá forçar todos os
 * usuários a fazerem login novamente.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         '-&A<bpyfDY:3T;@iCt5%4&Bppnd2>c!z%UGEE]0o;H9]z4L#YL;=10a+iF-kWMcQ' );
define( 'SECURE_AUTH_KEY',  '%Y.hMuR(j](|z7Gj8tmZ_0/I7X>3|G=mrW? P3j3]dL?<hDpkVn9ZVH{M,@<8~WG' );
define( 'LOGGED_IN_KEY',    'W&z)GkkVWJCwD-cfQ{}5*|wM|@L]uiywrZ/ =g;ibd?tr(#RQ$gjT-n3u3Ka@zom' );
define( 'NONCE_KEY',        'sLQTLi/V:a3./_PUGTz>i7p3^r2-OOccw2T1NKY^jR|2K)70Di|D<k1H=H1,,-ur' );
define( 'AUTH_SALT',        'f}U{X([fbl7x+Ss@>)nD[w|hNrylqst|p@khpt2?cLCaRxbsa[[!u(:(T>|8R<cX' );
define( 'SECURE_AUTH_SALT', '^pZA^x$G `#!Z8:W/*|T-z9)>;}SOT5,T=5x}+crA!Nwh}/CFE=]! Kge[6+t/B.' );
define( 'LOGGED_IN_SALT',   'c50s{z8+($H;sB`&_#+#t&kBkn4wYJUq?j),2$Xj4^70D|>u7{$TyVvf%JFSb}HU' );
define( 'NONCE_SALT',       'l~R[{0]8?~S*s@/Yq9BL9*ap!`+M&GUw@/3?xHKe~&B$r#_Dp%A:UJ7iSL*M{m&~' );

/**#@-*/

/**
 * Prefixo da tabela do banco de dados do WordPress.
 *
 * Você pode ter várias instalações em um único banco de dados se você der
 * um prefixo único para cada um. Somente números, letras e sublinhados!
 */
$table_prefix = 'wp_';

/**
 * Para desenvolvedores: Modo de debug do WordPress.
 *
 * Altere isto para true para ativar a exibição de avisos
 * durante o desenvolvimento. É altamente recomendável que os
 * desenvolvedores de plugins e temas usem o WP_DEBUG
 * em seus ambientes de desenvolvimento.
 *
 * Para informações sobre outras constantes que podem ser utilizadas
 * para depuração, visite o Codex.
 *
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', false );

/* Isto é tudo, pode parar de editar! :) */

/** Caminho absoluto para o diretório WordPress. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Configura as variáveis e arquivos do WordPress. */
require_once ABSPATH . 'wp-settings.php';
