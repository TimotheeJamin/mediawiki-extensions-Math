<?php
/**
 * MediaWiki math extension
 *
 * (c) 2002-2015 Tomasz Wegrzanowski, Brion Vibber, Moritz Schubotz,
 * and other MediaWiki contributors
 * GPLv2 license; info in main package.
 *
 * @author Moritz Schubotz
 */

class MathInputCheckRestbase extends MathInputCheck {
	private $restbaseInterface;

	/**
	 * Default constructor
	 * (performs no checking)
	 * @param string $tex the TeX input string to be checked
	 * @param string $type
	 */
	public function __construct( $tex = '', $type = 'tex' ) {
		parent::__construct( $tex );
		$this->restbaseInterface = new MathRestbaseInterface( $tex, $type );
	}

	/**
	 * @see https://phabricator.wikimedia.org/T119300
	 * @param stdClass $e
	 * @param MathRenderer $errorRenderer
	 * @return string
	 */
	public function errorObjectToHtml( stdClass $e, $errorRenderer = null ) {
		if ( $errorRenderer === null ) {
			$errorRenderer =  new MathSource( $this->inputTeX );
		}
		if ( isset( $e->error->message ) ){
			if ( $e->error->message === 'Illegal TeX function' ) {
				return $errorRenderer->getError( 'math_unknown_function', $e->error->found );
			} elseif ( preg_match( '/Math extension/', $e->error->message ) ) {
				$names = MathHooks::getMathNames();
				$mode = $names['mathml'];
				try {
					$host = $this->restbaseInterface->getUrl( '' );
				}
				catch ( Exception $ignore ) {
					$host = 'invalid';
				}
				$msg = $e->error->message;
				return $errorRenderer->getError( 'math_invalidresponse', $mode, $host, $msg );
			}
			return $errorRenderer->getError( 'math_syntax_error' );
		}
		return $errorRenderer->getError( 'math_unknown_error' );
	}
	/**
	 * @return boolean
	 */
	public function isValid() {
		return $this->restbaseInterface->checkTeX();
	}

	/**
	 * Some TeX checking programs may return
	 * a modified tex string after having checked it.
	 * You can get the altered tex string with this method
	 * @return string A valid Tex string
	 */
	public function getValidTex() {
		return $this->restbaseInterface->getCheckedTex();
	}


	/**
	 * Returns the string of the last error.
	 * @return string
	 */
	public function getError() {
		$err = $this->restbaseInterface->getError();
		if ( $err === null ){
			return null;
		}
		return $this->errorObjectToHtml( $err );
	}

	public function getRbi() {
		return $this->restbaseInterface;
	}

}
