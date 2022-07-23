<?php

use MediaWiki\Config\ServiceOptions;
use MediaWiki\Extension\Math\InputCheck\InputCheckFactory;
use MediaWiki\Extension\Math\MathConfig;
use MediaWiki\Extension\Math\MathWikibaseConnector;
use MediaWiki\Extension\Math\Render\RendererFactory;
use MediaWiki\Logger\LoggerFactory;
use MediaWiki\MediaWikiServices;
use Wikibase\Client\WikibaseClient;

return [
	'Math.CheckerFactory' => static function ( MediaWikiServices $services ): InputCheckFactory {
		return new InputCheckFactory(
			new ServiceOptions(
				InputCheckFactory::CONSTRUCTOR_OPTIONS,
				$services->getMainConfig()
			),
			$services->getMainWANObjectCache(),
			$services->getHttpRequestFactory(),
			LoggerFactory::getInstance( 'Math' )
		);
	},
	'Math.Config' => static function ( MediaWikiServices $services ): MathConfig {
		return new MathConfig(
			new ServiceOptions( MathConfig::CONSTRUCTOR_OPTIONS, $services->getMainConfig() )
		);
	},
	'Math.RendererFactory' => static function ( MediaWikiServices $services ): RendererFactory {
		return new RendererFactory(
			new ServiceOptions(
				RendererFactory::CONSTRUCTOR_OPTIONS,
				$services->getMainConfig()
			),
			$services->get( 'Math.Config' ),
			$services->getUserOptionsLookup(),
			LoggerFactory::getInstance( 'Math' )
		);
	},
	'Math.WikibaseConnector' => static function ( MediaWikiServices $services ): MathWikibaseConnector {
		return new MathWikibaseConnector(
			new ServiceOptions( MathWikibaseConnector::CONSTRUCTOR_OPTIONS, $services->getMainConfig() ),
			WikibaseClient::getRepoLinker( $services ),
			$services->getLanguageFactory(),
			WikibaseClient::getEntityRevisionLookup( $services ),
			WikibaseClient::getFallbackLabelDescriptionLookupFactory( $services ),
			WikibaseClient::getSite( $services ),
			WikibaseClient::getEntityIdParser( $services ),
			LoggerFactory::getInstance( 'Math' )
		);
	}
];
