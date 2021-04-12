<?php
/**
 * @author   Mahmoud Ahmed
 * @version  1.0.0
 */
defined('_JEXEC') or die;


use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Language\Text;

/**
 * ProgrammingTask Plugin.
 */
class PlgSystemProgrammingTask extends CMSPlugin
{
	/**
	 * Load the language file on instantiation.
	 *
	 * @var    boolean
	 * @since  3.9.0
	 */
	protected $autoloadLanguage = true;
	
	/**
	 * Application object.
	 *
	 * @var    \Joomla\CMS\Application\CMSApplication
	 * @since  3.8.0
	 */
	protected $app;

	/**
	 * This event is triggered immediately before the framework has rendered the application.
	 *
	 * @return  void
	 */
	public function onBeforeRender()
	{
		// Get the parameter values
		$lifetime = $this->params->get('cookie_lifetime', 60) * 24 * 60 * 60;
		$cookieName = $this->params->get("cookie_name", "is_visited_before");

		/*
		if($cookieName === '')
		{
			throw new \RuntimeException("ERROR_NO_COOKIE_NAME");
		}
		*/
		
		// Get cookie
		$cookieValue = $this->app->input->cookie->get($cookieName);

		// Load assets
		$this->app->getDocument()->getWebAssetManager()->registerAndUseScript(
			'plg_system_programming_task', 
			'plg_system_programming_task/app.js', 
			[], 
			['defer' => true]
		)->registerAndUseStyle(
			'plg_system_programming_task', 
			'plg_system_programming_task/main.css'
		);

		// Check if a cookie is set. 
		if(!$cookieValue)
		{
			// Set cookie with value
			$this->app->input->cookie->set(
				$cookieName,
				microtime(true),
				time() + $lifetime,
				$this->app->get('cookie_path', '/'),
				$this->app->get('cookie_domain', ''),
				$this->app->isHttpsForced(),
				true
			);
		}
		else
		{
			$dateTime = date("m-d-Y H:i:s", $cookieValue);
			
			// Create the document.
			$dom = new \DOMDocument('1.0', 'UTF-8');

			// Create a new div element
			$cookieBanner = $dom->createElement('div');
			$cookieBanner->setAttribute('id', "myDIV");
			$cookieBanner->setAttribute('class', "cookie-banner");

			// Create a new div element and set the attributes.
			$cookieBannerInner = $dom->createElement('div');
			$cookieBannerInner->setAttribute('class', "cookie-banner-inner");

			// Create a new div element and set the attributes.
			$cookieBannerCopy = $dom->createElement('div');
			$cookieBannerCopy->setAttribute('class', "cookie-banner-copy");

			// Create a new div element and set the attributes.
			$bannerHeader = Text::_('PLG_SYSTEM_PROGRAMING_TASK_COOKIE_BANNER_HEADER');
			$cookieBannerHeader = $dom->createElement('div', $bannerHeader);
			$cookieBannerHeader->setAttribute('class', "cookie-banner-header");
	
			// Create a new div element and set the attributes.
			$bannerDescription = Text::_('PLG_SYSTEM_PROGRAMING_TASK_COOKIE_BANNER_DESCRIPTION');
			$cookieBannerDescription = $dom->createElement('div', $bannerDescription . $dateTime);
			$cookieBannerDescription->setAttribute('class', "cookie-banner-description");

			// Create a new div element and set the attributes.
			$cookieBannerActions = $dom->createElement('div');
			$cookieBannerActions->setAttribute('class', "cookie-banner-actions");

			// Create a new button element and set the attributes.
			$cookieBannerCta = $dom->createElement('button', 'OK');
			$cookieBannerCta->setAttribute('class', "cookie-banner-cta");
			$cookieBannerCta->setAttribute('onclick', "myFunction();");

			// Append a cookieBannerInner in a cookieBanner
			$cookieBanner->appendChild($cookieBannerInner);

			// Append a cookieBannerCopy & cookieBannerActions in a cookieBannerInner
			$cookieBannerInner->appendChild($cookieBannerCopy);
			$cookieBannerInner->appendChild($cookieBannerActions);

			// Append a cookieBannerHeader & cookieBannerDescription in a cookieBannerCopy
			$cookieBannerCopy->appendChild($cookieBannerHeader);
			$cookieBannerCopy->appendChild($cookieBannerDescription);
			
			// Append a cookieBannerCta in a cookieBannerActions
			$cookieBannerActions->appendChild($cookieBannerCta);
	
			// Append the whole bunch.
			$dom->appendChild($cookieBanner);

			// Parse the HTML.
			echo $dom->saveHTML($cookieBanner);
		}
	}
}
