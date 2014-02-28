<?php
/**
 * [PHPFOX_HEADER]
 */
defined('PHPFOX') or exit('No direct script access allowed.');

/**
 *
 *
 * @copyright		Konsort.org
 * @author  		James
 * @package 		DVS
 */
class Dvs_Component_Ajax_Ajax extends Phpfox_Ajax
{
	public function generateShortUrl()
	{
		$iDvsId = $this->get('dvs_id');
		$sVideoRefId = $this->get('video_ref_id');
		$sService = $this->get('service');
		$sReturnId = $this->get('return_id');
		$iUserId = Phpfox::getUserId();

		$sShortUrl = Phpfox::getService('dvs.shorturl')->generate($iDvsId, $sVideoRefId, $sService, $iUserId);

		if (Phpfox::getParam('dvs.enable_subdomain_mode'))
		{
			$sUrl = Phpfox::getLib('url')->makeUrl('') . $sShortUrl;
		}
		else
		{
			$sUrl = Phpfox::getLib('url')->makeUrl('dvs') . $sShortUrl;
		}

		$this->val('#' . $sReturnId, $sUrl);
	}

	public function getPrice()
	{
		Phpfox::getBlock('dvs.get-price');
		$this->html('#dvs_get_price_container', $this->getContent(false));
		$this->show('#dvs_get_price_container', 'fast');
	}

	public function showShareEmail()
	{
		Phpfox::getBlock('dvs.share-email');
		$this->html('#dvs_share_email_wrapper', $this->getContent(false));
		$this->call('checkModernizr();');
		$this->show('#dvs_share_email_wrapper', 'fast');
	}

	public function deletePlayer()
	{
		if (!Phpfox::isAdmin())
		{
			return false;
		}

		$iPlayerId = $this->get('player_id');

		Phpfox::getService('dvs.player.process')->remove($iPlayerId);
	}

	public function deleteDvs()
	{
		$iDvsId = $this->get('dvs_id');

		if (!Phpfox::getService('dvs')->hasAccess($iDvsId, Phpfox::getUserId()))
		{
			return false;
		}

		Phpfox::getService('dvs.process')->remove($iDvsId);
	}

	public function deleteTheme()
	{
		$iThemeId = $this->get('theme_id');

		Phpfox::getService('dvs.theme.process')->remove($iThemeId);
	}

	public function logoFileProcess()
	{
		$iUserId = $this->get('user_id');
		$this->errorSet('#js_logo_file_upload_message');

		if ($iId = Phpfox::getService('dvs.file')->logoFileProcess($this->get('logo_file'), $this->get('logo_file_id')))
		{
			$sLogoFile = $this->get('logo_file');

			// windows
			if (strpos($sLogoFile, "\\"))
			{
				$aParts = explode('\\', $sLogoFile);
				if (isset($aParts[count($aParts) - 1]))
				{
					$sLogoFile = $aParts[count($aParts) - 1];
				}
			}

			$this->attr('#js_view_logo_file_link', 'href', Phpfox::getLib('url')->makeUrl('file', array('redirect' => $iId)))
				->html('#js_logo_upload_file_name', htmlentities(addslashes($sLogoFile)))
				->val('.js_cache_logo_file_id', $iId)
				->submit('#js_logo_file_form')
				->show('#js_logo_file_process');
		}
		else
		{
			$this->show('#js_logo_file_upload_error');
		}
	}

	public function brandingFileProcess()
	{
		$iUserId = $this->get('user_id');
		$this->errorSet('#js_branding_file_upload_message');

		if ($iId = Phpfox::getService('dvs.file')->brandingFileProcess($this->get('branding_file'), $this->get('branding_file_id')))
		{
			$sBrandingFile = $this->get('branding_file');

			// windows
			if (strpos($sBrandingFile, "\\"))
			{
				$aParts = explode('\\', $sBrandingFile);
				if (isset($aParts[count($aParts) - 1]))
				{
					$sBrandingFile = $aParts[count($aParts) - 1];
				}
			}

			$this->attr('#js_view_branding_file_link', 'href', Phpfox::getLib('url')->makeUrl('file', array('redirect' => $iId)))
				->html('#js_branding_upload_file_name', htmlentities(addslashes($sBrandingFile)))
				->val('.js_cache_branding_file_id', $iId)
				->submit('#js_branding_file_form')
				->show('#js_branding_file_process');
		}
		else
		{
			$this->show('#js_branding_file_upload_error');
		}
	}

	public function backgroundFileProcess()
	{
		$iUserId = $this->get('user_id');
		$this->errorSet('#js_background_file_upload_message');

		if ($iId = Phpfox::getService('dvs.file')->backgroundFileProcess($this->get('background_file'), $this->get('background_file_id')))
		{
			$sBackgroundFile = $this->get('background_file');

			// windows
			if (strpos($sBackgroundFile, "\\"))
			{
				$aParts = explode('\\', $sBackgroundFile);
				if (isset($aParts[count($aParts) - 1]))
				{
					$sBackgroundFile = $aParts[count($aParts) - 1];
				}
			}

			$this->attr('#js_view_background_file_link', 'href', Phpfox::getLib('url')->makeUrl('file', array('redirect' => $iId)))
				->html('#js_background_upload_file_name', htmlentities(addslashes($sBackgroundFile)))
				->val('.js_cache_background_file_id', $iId)
				->submit('#js_background_file_form')
				->show('#js_background_file_process');
		}
		else
		{
			$this->show('#js_background_file_upload_error');
		}
	}

	public function prerollFileProcess()
	{
		$iUserId = $this->get('user_id');
		$this->errorSet('#js_preroll_file_upload_message');

		if ($iId = Phpfox::getService('dvs.file')->prerollFileProcess($this->get('preroll_file'), $this->get('preroll_file_id')))
		{
			$sPrerollFile = $this->get('preroll_file');

			// windows
			if (strpos($sPrerollFile, "\\"))
			{
				$aParts = explode('\\', $sPrerollFile);
				if (isset($aParts[count($aParts) - 1]))
				{
					$sPrerollFile = $aParts[count($aParts) - 1];
				}
			}

			$this->attr('#js_view_preroll_file_link', 'href', Phpfox::getLib('url')->makeUrl('file', array('redirect' => $iId)))
				->html('#js_preroll_upload_file_name', htmlentities(addslashes($sPrerollFile)))
				->val('.js_cache_preroll_file_id', $iId)
				->submit('#js_preroll_file_form')
				->show('#js_preroll_file_process');
		}
		else
		{
			$this->show('#js_preroll_file_upload_error');
		}
	}

	public function removeBrandingFile()
	{
		$iBrandingId = $this->get('iBrandingFileId');

		if (!Phpfox::getService('dvs')->hasAccess($iBrandingId, Phpfox::getUserId(), 'branding'))
		{
			return false;
		}

		Phpfox::getService('dvs.file.process')->removeBranding($iBrandingId);
	}

	public function removeBackgroundFile()
	{
		$iBackgroundId = $this->get('iBackgroundFileId');

		if (!Phpfox::getService('dvs')->hasAccess($iBackgroundId, Phpfox::getUserId(), 'background'))
		{
			return false;
		}

		Phpfox::getService('dvs.file.process')->removeBackground($iBackgroundId);
	}

	public function removeLogoFile()
	{
		$iLogoId = $this->get('iLogoFileId');

		if (!Phpfox::getService('dvs')->hasAccess($iLogoId, Phpfox::getUserId(), 'logo'))
		{
			return false;
		}

		Phpfox::getService('dvs.file.process')->removeLogo($iLogoId);
	}

	public function removePrerollFile()
	{
		$iPrerollId = $this->get('iPrerollFileId');

		if (!Phpfox::getService('dvs')->hasAccess($iPrerollId, Phpfox::getUserId(), 'preroll'))
		{
			return false;
		}

		Phpfox::getService('dvs.file.process')->removePreRoll($iPrerollId);
	}

	public function moreInfoPlayerType()
	{
		$this->template()->getTemplate('dvs.block.more-info.player-type');
	}

	public function moreInfoDomainName()
	{
		$this->template()->getTemplate('dvs.block.more-info.domain-name');
	}

	public function moreInfoLogoBranding()
	{
		$this->template()->getTemplate('dvs.block.more-info.logo-branding');
	}

	public function moreInfoPrerollSwf()
	{
		$this->template()->getTemplate('dvs.block.more-info.preroll-swf');
	}

	public function moreInfoPrerollDuration()
	{
		$this->template()->getTemplate('dvs.block.more-info.preroll-duration');
	}

	public function previewPlayer()
	{
		$aVals = Phpfox::getLib('request')->getArray('val');
		$bMakeSelected = false;

		foreach ($aVals['selected_makes'] as $sMake => $bSelected)
		{
			if ($bSelected)
			{
				$bMakeSelected = true;
			}
		}

		if (!$bMakeSelected)
		{
			echo Phpfox::getPhrase('dvs.strong_error_you_must_select_at_least_1_make_before_previewing_the_player_strong');
		}
		else
		{
			Phpfox::getBlock('dvs.player-preview', array('aVals' => $aVals));
		}
	}

	public function updateTitleUrl()
	{
		$sVanityUrl = Phpfox::getLib('request')->get('vanity_url');
		$iDvsId = Phpfox::getLib('request')->get('dvs_id');

		// Are we editing the current Vanity Url?
		if(!empty($iDvsId)){
			$sTitleUrl = Phpfox::getService('dvs')->getTitleUrl($sVanityUrl, $iDvsId);
		}
		else{
			$sTitleUrl = Phpfox::getService('dvs')->getTitleUrl($sVanityUrl);
		}
		
		$this->call('$("#title_url").val("' . $sTitleUrl . '");');
		if (Phpfox::getParam('dvs.enable_subdomain_mode'))
		{
			$this->call('$("#title_url_display").html("' . Phpfox::getLib('url')->makeUrl($sTitleUrl) . '");');
		}
		else
		{
			$this->call('$("#title_url_display").html("' . Phpfox::getLib('url')->makeUrl('dvs', $sTitleUrl) . '");');
		}
	}

	public function changeVideo()
	{
		//Change RefID for contact form
		$sRefId = Phpfox::getLib('request')->get('sRefId');
		Phpfox::getService('dvs.video')->setDvs(Phpfox::getLib('request')->get('iDvsId'));
		$aVideo = Phpfox::getService('dvs.video')->get($sRefId);
		$aDvs = Phpfox::getService('dvs')->get(Phpfox::getLib('request')->get('iDvsId'));
		
		// Change get price form values
//		$this->html('.vehicle_year', $aVideo['year']);
//		$this->html('.vehicle_make', $aVideo['make']);
//		$this->html('.vehicle_model', $aVideo['model']);

//		$this->val('#contact_video_ref_id', $aVideo['referenceId']);

		if (empty($aDvs) || empty($aVideo))
		{
			return false;
		}

		$aDvs['phrase_overrides'] = Phpfox::getService('dvs.override')->getAll($aDvs, $aVideo);
		$bVideoChanged = ($this->get('bVideoChanged') == 'true' ? true : false);

		if (Phpfox::getParam('dvs.enable_subdomain_mode'))
		{
			$sOverrideLink = Phpfox::getLib('url')->makeUrl($aDvs['title_url'], $aVideo['video_title_url']);
		}
		else
		{
			$sOverrideLink = Phpfox::getLib('url')->makeUrl('dvs', array($aDvs['title_url'], $aVideo['video_title_url']));
		}

		$sOverrideLink = rtrim($sOverrideLink, '/');

		//Change video information and reset description visibility
		$this->html('#video_name', '<a href="' . $sOverrideLink . '">' . $aDvs['phrase_overrides']['override_video_name_display'] . '</a>');
		$this->html('#model_description', Phpfox::getLib('parse.output')->clean($aDvs['phrase_overrides']['override_video_description_display']));
//		$this->html('#video_name', '<strong><a href="' . $sOverrideLink . '">' . $aDvs['phrase_overrides']['override_video_name_display'] . '</a></strong>');
//		$this->html('#video_long_description_text', Phpfox::getLib('parse.output')->clean($aDvs['phrase_overrides']['override_video_description_display']));
//		$this->html('#video_long_description_shortened_text', Phpfox::getLib('parse.output')->shorten(Phpfox::getLib('parse.output')->clean($aDvs['phrase_overrides']['override_video_description_display']), Phpfox::getParam('dvs.long_desc_limit'), '...'));
//
//		$this->hide('#video_long_description');
//		$this->show('#video_long_description_shortened');

//		if (strlen(Phpfox::getLib('parse.output')->clean($aVideo['longDescription'])) > Phpfox::getParam('dvs.long_desc_limit'))
//		{
//			$this->show('#video_long_description_control');
//			$this->show('#video_long_description_shortened_control');
//		}
//		else
//		{
//			$this->hide('#video_long_description_control');
//			$this->hide('#video_long_description_shortened_control');
//		}

		// Change microdata
		$this->call('$("#schema_video_thumbnail_url").attr("content", "' . Phpfox::getLib('url')->makeUrl((Phpfox::getParam('dvs.enable_subdomain_mode') ? 'www.' : '') . 'file.brightcove') . $aVideo['thumbnail_image'] . '");');
		$this->call('$("#schema_video_image").attr("content", "' . Phpfox::getLib('url')->makeUrl((Phpfox::getParam('dvs.enable_subdomain_mode') ? 'www.' : '') . 'file.brightcove') . $aVideo['video_still_image'] . '");');
		$this->call('$("#schema_video_embed_url").attr("content", "http://c.brightcove.com/services/viewer/federated_f9/1970101121001?isVid=1&isUI=1&domain=embed&playerID=1970101121001&publisherID=607012070001&videoID=' . $aVideo['referenceId'] . '");');
		$this->call('$("#schema_video_upload_date").attr("content", "' . date('Y-m-d', (int) ($aVideo['publishedDate'] / 1000)) . '");');
		$this->call('$("#schema_video_duration").attr("content", "PT' . (int) ($aVideo['length'] / 1000) . 'S");');
		$this->call('$("#schema_video_name").attr("content", "' . $aDvs['phrase_overrides']['override_video_name_display'] . '");');
		$this->call('$("#schema_video_description").attr("content", "' . Phpfox::getLib('parse.output')->clean($aDvs['phrase_overrides']['override_video_description_display']) . '");');

		// Change inventory link URL
		$sInventoryLink = str_replace('{$sMake}', urlencode($aVideo['make']), html_entity_decode($aDvs['inventory_url']));
		$sInventoryLink = str_replace('{$sModel}', urlencode($aVideo['model']), $sInventoryLink);
		$sInventoryLink = str_replace('{$iYear}', urlencode($aVideo['year']), $sInventoryLink);

		if (Phpfox::getParam('dvs.javascript_debug_mode'))
		{
			$this->call('console.log("Page: Inventory link: ' . $aDvs['inventory_url'] . '");');
			$this->call('console.log("Page: Setting Inventory link: ' . $sInventoryLink . '");');
		}

		$this->call('$(".dvs_inventory_link").attr("href", "' . $sInventoryLink . '");');

		//Change address bar contents
		$sBrowser = Phpfox::getService('dvs')->getBrowser();

		$sTitle = $aDvs['phrase_overrides']['override_page_title_display_video_specified'];
		$sUrl = $aVideo['video_title_url'];

		// Only change the URL if the video is not the default video
		if ($bVideoChanged)
		{
			if (Phpfox::getParam('dvs.javascript_debug_mode'))
			{
				$this->call('console.log("AJAX: Video is changed.  Changing URL...");');
			}

			$this->call('window.parent.history.pushState("string", "' . $sTitle . '", "' . $sUrl . '");');

			// Most browsers do not support changing the page title via pushState
			$this->call('document.title = "' . $sTitle . '";');
		}
		else
		{
			if (Phpfox::getParam('dvs.javascript_debug_mode'))
			{
				$this->call('console.log("AJAX: Video is unchanged.");');
			}
		}

		// Change share links
		$this->call('sShareLink = "' . $sOverrideLink . '";');

		if (Phpfox::getParam('dvs.javascript_debug_mode'))
		{
			$this->call('console.log("Page: Setting share URL to: ' . Phpfox::getLib('url')->makeUrl((Phpfox::getService('dvs')->getCname() ? Phpfox::getService('dvs')->getCname() : 'dvs'), $sUrl) . '");');
		}

		if ($sBrowser == 'mobile' || $sBrowser == 'ipad')
		{
			$this->attr('#bc_player_param_linkbase', 'value', Phpfox::getLib('url')->makeUrl((Phpfox::getService('dvs')->getCname() ? Phpfox::getService('dvs')->getCname() : 'dvs'), $sUrl));
		}
		else
		{
			$this->call('modSoc.setLink("' . Phpfox::getLib('url')->makeUrl((Phpfox::getService('dvs')->getCname() ? Phpfox::getService('dvs')->getCname() : 'dvs'), $sUrl) . '");');
		}

		$this->call('$("#schema_video_url").attr("content", "' . Phpfox::getLib('url')->makeUrl((Phpfox::getService('dvs')->getCname() ? Phpfox::getService('dvs')->getCname() : 'dvs'), $sUrl) . '");');

		// Change twitter default text
		// Repllace variables in the subject
		$aFindReplace = array();
		foreach ($aDvs as $sKey => $sValue)
		{
			if ($sKey == 'phrase_overrides')
			{
				continue;
			}

			$aFind[] = '{dvs_' . $sKey . '}';
			$aReplace[] = '' . $sValue . '';
		}

		foreach ($aVideo as $sKey => $sValue)
		{
			$aFind[] = '{video_' . $sKey . '}';
			$aReplace[] = '' . $sValue . '';
		}

		if ($bVideoChanged)
		{
			$sTwitterText = Phpfox::getPhrase('dvs.twitter_default_share_text');
			$sTwitterText = str_replace($aFind, $aReplace, $sTwitterText);

			$sVideoUrl = (Phpfox::getParam('dvs.enable_subdomain_mode') ? Phpfox::getLib('url')->makeUrl($aDvs['title_url'], $aVideo['video_title_url']) : Phpfox::getLib('url')->makeUrl('dvs', array($aDvs['title_url'], $aVideo['video_title_url'])));
			$this->remove('.twitter_popup');
			$this->html('#twitter_button_wrapper', '<a href="https://twitter.com/share?url=' . urlencode($sVideoUrl) . '&text=' . urlencode($sTwitterText) . '" class="twitter-share-button twitter_popup" data-size="large" data-count="none" id="dvs_twitter_share_link"></a>');
			$this->call('twttr.widgets.load();');
		}

		$this->val('#contact_dvs_id', $aDvs['dvs_id']);
	}

	public function contactDealer()
	{
		$aVals = Phpfox::getLib('request')->getArray('val');
		$bIsError = false;

		if (!$aVals['contact_name'] && Phpfox::getParam('dvs.get_price_validate_name'))
		{
			Phpfox_Error::set(Phpfox::getPhrase('dvs.please_enter_your_name'). ' ');
			$bIsError = true;
		}
		if (!$aVals['contact_email'] && Phpfox::getParam('dvs.get_price_validate_email'))
		{
			Phpfox_Error::set(Phpfox::getPhrase('dvs.please_enter_your_email_address'). ' ');
			$bIsError = true;
		}
		if (!$aVals['contact_phone'] && Phpfox::getParam('dvs.get_price_validate_phone'))
		{
			Phpfox_Error::set(Phpfox::getPhrase('dvs.please_enter_your_phone_number'). ' ');
			$bIsError = true;
		}
		if (!$aVals['contact_zip'] && Phpfox::getParam('dvs.get_price_validate_zip_code'))
		{
			Phpfox_Error::set(Phpfox::getPhrase('dvs.please_enter_your_zip_code'). ' ');
			$bIsError = true;
		}
		if (!$aVals['contact_comments'] && Phpfox::getParam('dvs.get_price_validate_comments'))
		{
			Phpfox_Error::set(Phpfox::getPhrase('dvs.please_enter_comments'). ' ');
			$bIsError = true;
		}

		if (!$bIsError)
		{
			$aVideo = Phpfox::getService('dvs.video')->get($aVals['contact_video_ref_id']);
			$aDvs = Phpfox::getService('dvs')->get($aVals['contact_dvs_id']);

			$sSubject = Phpfox::getPhrase('dvs.dealer_email_subject', array(
					'contact_name' => $aVals['contact_name'],
					'contact_email' => $aVals['contact_email'],
					'contact_phone' => $aVals['contact_phone'],
					'contact_zip' => $aVals['contact_zip'],
					'contact_comments' => $aVals['contact_comments'],
					'year' => $aVideo['year'],
					'make' => $aVideo['make'],
					'model' => $aVideo['model'],
					'bodyStyle' => $aVideo['bodyStyle'],
					'dvs_name' => $aDvs['dvs_name'],
					'dealer_name' => $aDvs['dealer_name'],
					'title_url' => $aDvs['title_url'],
					'address' => $aDvs['address'],
					'city' => $aDvs['city'],
					'state_string' => $aDvs['state_string'],
					'phone' => $aDvs['phone']
			));

			$sBody = Phpfox::getPhrase('dvs.dealer_email_body', array(
					'contact_name' => $aVals['contact_name'],
					'contact_email' => $aVals['contact_email'],
					'contact_phone' => $aVals['contact_phone'],
					'contact_zip' => $aVals['contact_zip'],
					'contact_comments' => $aVals['contact_comments'],
					'year' => $aVideo['year'],
					'make' => $aVideo['make'],
					'model' => $aVideo['model'],
					'bodyStyle' => $aVideo['bodyStyle'],
					'dvs_name' => $aDvs['dvs_name'],
					'dealer_name' => $aDvs['dealer_name'],
					'title_url' => $aDvs['title_url'],
					'address' => $aDvs['address'],
					'city' => $aDvs['city'],
					'state_string' => $aDvs['state_string'],
					'phone' => $aDvs['phone']
			));

			Phpfox::getLib('mail')
				->to($aDvs['email'])
				->subject($sSubject)
				->message($sBody)
				->send();

			Phpfox::getService('dvs.process')->updateContactCount($aDvs['dvs_id']);

			$this->hide('#contact_dealer');
			$this->show('#dvs_contact_success');
			$this->call('getPriceEmailSent();');
		}
		else
		{
			return false;
		}
	}

	public function sendShareEmail()
	{
		$aVals = Phpfox::getLib('request')->getArray('val');
		$bIsError = false;

		if (!$aVals['share_name'])
		{
			Phpfox_Error::set(Phpfox::getPhrase('dvs.please_enter_an_email_address'));
			$bIsError = true;
		}
		if (!$aVals['my_share_name'])
		{
			Phpfox_Error::set(Phpfox::getPhrase('dvs.please_enter_your_name'));
			$bIsError = true;
		}

		if (!$aVals['share_email'])
		{
			Phpfox_Error::set(Phpfox::getPhrase('dvs.please_enter_your_friends_name'));
			$bIsError = true;
		}

		if (!$bIsError)
		{
			$aDvs = Phpfox::getService('dvs')->get($aVals['dvs_id']);
			Phpfox::getService('dvs.video')->setDvs($aDvs['dvs_id']);
			$aVideo = Phpfox::getService('dvs.video')->get($aVals['video_ref_id']);

			$sSubject = Phpfox::getPhrase('dvs.share_email_subject');

			// Repllace variables in the subject
			$aFindReplace = array();
			foreach ($aVals as $sKey => $sValue)
			{
				$aFind[] = '{share_' . $sKey . '}';
				$aReplace[] = '' . $sValue . '';
			}
			foreach ($aDvs as $sKey => $sValue)
			{
				$aFind[] = '{dvs_' . $sKey . '}';
				$aReplace[] = '' . $sValue . '';
			}
			foreach ($aVideo as $sKey => $sValue)
			{
				$aFind[] = '{video_' . $sKey . '}';
				$aReplace[] = '' . $sValue . '';
			}

			$sSubject = str_replace($aFind, $aReplace, $sSubject);

			Phpfox::getBlock('dvs.share-email-template', array(
				'iDvsId' => $aDvs['dvs_id'],
				'sReferenceId' => $aVideo['referenceId'],
				'sShareName' => $aVals['share_name'],
				'sMyShareName' => $aVals['my_share_name'],
				'sShareMessage' => $aVals['share_message'],
				'sShareEmail' => $aVals['share_email'],
				'sBackgroundImageUrl' => ($aDvs['background_file_name'] ? Phpfox::getLib('url')->makeUrl((Phpfox::getParam('dvs.enable_subdomain_mode') ? 'www.' : '') . 'file.dvs.background') . $aDvs['background_file_name'] : ''),
				'sVideoLink' => (Phpfox::getParam('dvs.enable_subdomain_mode') ? Phpfox::getLib('url')->makeUrl($aDvs['title_url'], $aVideo['video_title_url']) : Phpfox::getLib('url')->makeUrl('dvs', array($aDvs['title_url'], $aVideo['video_title_url']))),
				'sImagePath' => (Phpfox::getParam('dvs.enable_subdomain_mode') ? Phpfox::getLib('url')->makeUrl('www.module.dvs.static.image') : Phpfox::getLib('url')->makeUrl('module.dvs.static.image'))
			));
			$sBody = $this->getContent(false);

			$sDealerEmail = 'noreply@' . str_replace('www.', '', parse_url($aDvs['url'], PHP_URL_HOST));
//			Phpfox::getLibClass('phpfox.mail.interface');
//			$oMail = Phpfox::getLib('mail.driver.phpmailer.' . Phpfox::getParam('core.method'));
//			$oMail->send($aVals['share_email'], $sSubject, ' ', $sBody, $aVals['my_share_name'], $sDealerEmail);

			Phpfox::getLib('mail')
				->to($aVals['share_email'])
				->fromEmail($sDealerEmail)
				->subject($sSubject)
				->message($sBody)
				->send();
			
			$this->hide('#share_email_dealer');
			$this->show('#dvs_share_email_success');
		}
		else
		{
			return false;
		}
	}

	public function chooseTheme()
	{
		$iThemeId = Phpfox::getLib('request')->get('theme_id');

		if ($iThemeId)
		{
			$aTheme = Phpfox::getService('dvs.theme')->get($iThemeId);

			$this->call("$('#color_picker_menu_background div').css('background', '#" . $aTheme['theme_menu_background'] . "');");
			$this->call("$('#color_picker_menu_background_input').val('" . $aTheme['theme_menu_background'] . "');");
			$this->call("$('#preview_menu_container').css('background', '#" . $aTheme['theme_menu_background'] . "');");
			$this->call("$('#preview_contact_container').css('background', '#" . $aTheme['theme_menu_background'] . "');");

			$this->call("$('#color_picker_menu_link div').css('background', '#" . $aTheme['theme_menu_link'] . "');");
			$this->call("$('#color_picker_menu_link_input').val('" . $aTheme['theme_menu_link'] . "');");
			$this->call("$('#preview_menu_container').css('color', '#" . $aTheme['theme_menu_link'] . "');");
			$this->call("$('.dvs_top_menu_link').css('color', '#" . $aTheme['theme_menu_link'] . "');");


			$this->call("$('#color_picker_page_background div').css('background', '#" . $aTheme['theme_page_background'] . "');");
			$this->call("$('#color_picker_page_background_input').val('" . $aTheme['theme_page_background'] . "');");
			$this->call("$('#dvs_container').css('background', '#" . $aTheme['theme_page_background'] . "');");

			$this->call("$('#color_picker_page_text div').css('background', '#" . $aTheme['theme_page_text'] . "');");
			$this->call("$('#color_picker_page_text_input').val('" . $aTheme['theme_page_text'] . "');");
			$this->call("$('.preview_dealer_info').css('color', '#" . $aTheme['theme_page_text'] . "');");
			$this->call("$('#preview_vehicle_select_container').css('color', '#" . $aTheme['theme_page_text'] . "');");
			$this->call("$('#preview_now_playing_container').css('color', '#" . $aTheme['theme_page_text'] . "');");

			$this->call("$('#color_picker_button_background div').css('background', '#" . $aTheme['theme_button_background'] . "');");
			$this->call("$('#color_picker_button_background_input').val('" . $aTheme['theme_button_background'] . "');");
			$this->call("$('.preview_select').css('background', '#" . $aTheme['theme_button_background'] . "');");

			$this->call("$('#color_picker_button_text div').css('background', '#" . $aTheme['theme_button_text'] . "');");
			$this->call("$('#color_picker_button_text_input').val('" . $aTheme['theme_button_text'] . "');");
			$this->call("$('.dvs_c2a_button').css('color', '#" . $aTheme['theme_button_text'] . "');");
			$this->call("$('.dvs_c2a_button:hover').css('color', '#" . $aTheme['theme_button_text'] . "');");
			$this->call("$('.preview_select').css('color', '#" . $aTheme['theme_button_text'] . "');");

			$this->call("$('#color_picker_button_top_gradient div').css('background', '#" . $aTheme['theme_button_top_gradient'] . "');");
			$this->call("$('#color_picker_button_top_gradient_input').val('" . $aTheme['theme_button_top_gradient'] . "');");

			$this->call("$('#color_picker_button_bottom_gradient div').css('background', '#" . $aTheme['theme_button_bottom_gradient'] . "');");
			$this->call("$('#color_picker_button_bottom_gradient_input').val('" . $aTheme['theme_button_bottom_gradient'] . "');");

			$this->call("$('#color_picker_button_border div').css('background', '#" . $aTheme['theme_button_border'] . "');");
			$this->call("$('#color_picker_button_border_input').val('" . $aTheme['theme_button_border'] . "');");
			$this->call("$('.preview_select').css('borderColor', '#" . $aTheme['theme_button_border'] . "');");
			$this->call("$('.dvs_c2a_button').css('borderColor', '#" . $aTheme['theme_button_border'] . "');");

			$this->call("$('#color_picker_text_link div').css('background', '#" . $aTheme['theme_text_link'] . "');");
			$this->call("$('#color_picker_text_link_input').val('" . $aTheme['theme_text_link'] . "');");
			$this->call("$('#preview_dealer_website_link').css('color', '#" . $aTheme['theme_text_link'] . "');");

			$this->call("$('#color_picker_footer_link div').css('background', '#" . $aTheme['theme_footer_link'] . "');");
			$this->call("$('#color_picker_footer_link_input').val('" . $aTheme['theme_footer_link'] . "');");
			$this->call("$('.dvs_footer_link').css('color', '#" . $aTheme['theme_footer_link'] . "');");

			//Change preview buttons
			$this->call("$('.dvs_c2a_button').css('background', '-webkit-gradient( linear, left top, left bottom, color-stop(0.05, #" . $aTheme['theme_button_top_gradient'] . "), color-stop(1, #" . $aTheme['theme_button_bottom_gradient'] . ") )');");
			$this->call("$('.dvs_c2a_button').css('background', '-moz-linear-gradient( center top, #" . $aTheme['theme_button_top_gradient'] . " 5%, #" . $aTheme['theme_button_bottom_gradient'] . " 100% )');");
			$this->call("$('.dvs_c2a_button').css('filter', 'progid:DXImageTransform.Microsoft.gradient(startColorstr=\"#" . $aTheme['theme_button_top_gradient'] . "\", endColorstr=\"#" . $aTheme['theme_button_bottom_gradient'] . "\")');");
			$this->call("$('.dvs_c2a_button').css('backgroundColor', '#" . $aTheme['theme_button_top_gradient'] . "');");
			$this->call("$('.dvs_c2a_button:hover').css('background', '-webkit-gradient( linear, left top, left bottom, color-stop(0.05, #" . $aTheme['theme_button_top_gradient'] . "), color-stop(1, #" . $aTheme['theme_button_bottom_gradient'] . ") )');");
			$this->call("$('.dvs_c2a_button:hover').css('background', '-moz-linear-gradient( center top, #" . $aTheme['theme_button_top_gradient'] . " 5%, #" . $aTheme['theme_button_bottom_gradient'] . " 100% )');");
			$this->call("$('.dvs_c2a_button:hover').css('filter', 'progid:DXImageTransform.Microsoft.gradient(startColorstr=\"#" . $aTheme['theme_button_top_gradient'] . "\", endColorstr=\"#" . $aTheme['theme_button_bottom_gradient'] . "\")');");
			$this->call("$('.dvs_c2a_button:hover').css('backgroundColor', '#" . $aTheme['theme_button_bottom_gradient'] . "');");
		}
	}

	public function getMakes()
	{
		// Get the variables from the ajax call.
		$sDvsName = $this->get('sDvsName');
		$iYear = $this->get('iYear');

		// Get the DVS details based off the DVS name.
		$aDvs = Phpfox::getService('dvs')->get($sDvsName, true);
		$aPlayer = Phpfox::getService('dvs.player')->get($aDvs['dvs_id']);

		// Get all of the makes for the DVS for the selected year.
		$aMakes = Phpfox::getService('dvs.video')->getValidVSMakes($iYear, $aPlayer['makes']);

		// Did we get more than one make?
		if (count($aMakes) === 1)
		{
			$this->call('console.log("'.$aMakes[0]['make'].'");');
			// Yes, make the only make selected by default.
			$sSelectOptions = '<li class="init">' . $aMakes[0]['make'] . '</li><ul>';
			$this->call('$.ajaxCall(\'dvs.getModels\', \'iYear=' . $iYear . '&sMake=' . $aMakes[0]['make'] . '\');');
		}
		else
		{
			// The first list item should be one to tell the user to select a make.
			$sSelectOptions = '<li class="init">' . Phpfox::getPhrase('dvs.select_make') . '</li><ul>';
			$this->html('#models', '<li class="init">' . Phpfox::getPhrase('dvs.select_model') . '</li><ul><li>' . Phpfox::getPhrase('dvs.please_select_a_make_first') . '</li></ul>');
		}
		
		// Build the ul list items
		foreach ($aMakes as $aMake)
		{
			$sSelectOptions .= '<li onclick="$.ajaxCall(\'dvs.getModels\', \'iYear=' . $iYear . '&amp;sMake=' . $aMake['make'] . '\');">' . $aMake['make'] . '</li>';
		}
			
		$sSelectOptions .= '</ul>';

		// Replace the old html with the new list items.
		$this->html('#makes', $sSelectOptions);

//		if (Phpfox::getParam('dvs.enable_subdomain_mode'))
//		{
//			$sDvsTitle = $this->get('req1');
//		}
//		else
//		{
//			$sDvsTitle = $this->get('req1');
//		}
//		$aDvs = Phpfox::getService('dvs')->get($sDvsTitle, true);
//		$aPlayer = Phpfox::getService('dvs.player')->get($aDvs['dvs_id']);
//
//		$iYear = $this->get('iYear');
//		
//		if (!$iYear || !$aDvs)
//		{
//			return false;
//		}
//
//		$sSelectOptions = '';
//
//		$aMakes = Phpfox::getService('dvs.video')->getValidVSMakes($iYear, $aPlayer['makes']);
//
//		$sBrowser = Phpfox::getService('dvs')->getBrowser();
//
//		if ($sBrowser == 'mobile' || $sBrowser == 'ipad')
//		{
//			$sSelect = '<select id="dvs_video_select_make" class="dvs_select" onchange="$.ajaxCall(\'dvs.getModels\', \'sMake=\' + this.value + \'&amp;iYear=\'+$(\'#dvs_video_select_year\').val());"><option value="">' . Phpfox::getPhrase('dvs.select_make') . '</option>';
//			foreach ($aMakes as $aMake)
//			{
//				$sSelect .= '<option value="' . $aMake['make'] . '"' . (count($aMakes) == 1 ? ' selected="selected"' : '') . '>' . $aMake['make'] . '</option>';
//			}
//			$sSelect .= '</select>';
//		}
//		else
//		{
//
//			foreach ($aMakes as $aMake)
//			{
//				$sSelectOptions .= '<li><a href="#" onclick="$(\'#dvs_select_box_make_text\').html(\'' . $aMake['make'] . '\');$.ajaxCall(\'dvs.getModels\', \'iYear=' . $iYear . '&amp;sMake=' . $aMake['make'] . '&amp;sPlaylistBorder=\' + $(\'#dvs_playlist_border_color\').val());">' . $aMake['make'] . '</a></li>';
//			}
//
//			$sSelect = '<div class="dvs_select_box_anchor" data-dropdown="#dvs_video_select_make" data-vertical-offset="15"><div class="dvs_select_box_anchor_text" id="dvs_select_box_make_text">' . Phpfox::getPhrase('dvs.select_make') . '</div></div>';
//			$sSelect .= '<div class="dropdown dvs_select_options_container dropdown-anchor-right dropdown-relative" id="dvs_video_select_make">';
//			$sSelect .= '<ul class="dropdown-menu">';
//
//			if ($sSelectOptions)
//			{
//				$sSelect .= $sSelectOptions;
//			}
//			else
//			{
//				$sSelect .= '<li><a href="#">No Makes Found</a></li>';
//			}
//
//			$sSelect .= '</ul>';
//			$sSelect .= '</div>';
//
//			$this->html('#dvs_select_box_model_text', Phpfox::getPhrase('dvs.select_model'));
//			$this->val('#dvs_video_select_model_input', '');
//		}
//		echo 'test';
//		$this->html('#makes', $sSelectOptions);
////		$this->remove('.dvs_year_reset');
////		$this->html('#dvs_vehicle_select_make_container', $sSelect);
////		$this->html('#dvs_vehicle_select_model_placeholder', '<a href="#">' . Phpfox::getPhrase('dvs.please_select_a_make_first') . '</a>');
//
//		if (count($aMakes) == 1)
//		{
//			$this->getModels($iYear, $aMakes[0]['make']);
//		}
	}

	public function getModels()
	{
		// Set the variables to determine which models to get.
		$sMake = $this->get('sMake');
		$iYear = $this->get('iYear');
		$this->call('console.log("'.$iYear.'");');
		$this->call('console.log("'.$sMake.'");');
		// Get a list of models that belong to the make and year.
		$aModels = Phpfox::getService('dvs.video')->getVideoSelect($iYear, $sMake, '', true);

		// Are there models to add to the drop down menu?
		if (!empty($aModels))
		{
			// Yes, begin to create the drop down menu.
			$sSelectOptions = '<li class="init">' . Phpfox::getPhrase('dvs.select_model') . '</li><ul>';
		}
		else
		{
			// No, let the user know there were no models found.
			$sSelectOptions = '<li class="init">No Models Found</li><ul>';
		}

		// Add each model to the drop down.
		foreach ($aModels as $aModel)
		{
			$sSelectOptions .= '<li onclick="$.ajaxCall(\'dvs.videoSelect\', \'sModel=' . $aModel['model'] . '&amp;iYear=' . $aModel['year'] . '&amp;sMake=' . $aModel['make'] . '&amp;iDvsId=\' + $(\'#contact_dvs_id\').val() + \'&amp;sPlaylistBorder=\' + $(\'#dvs_playlist_border_color\').val());">' . $aModel['year'] . ' ' . $aModel['model'] . (Phpfox::getParam('dvs.javascript_debug_mode') ? ' (' . $aModel['video_type'] . ')' : '') . '</li>';
		}
		
		$sSelectOptions .= '</ul>';

		// Display the dropdown on the page.
		$this->html('#models', $sSelectOptions);

//		if (!$iYear || !$sMake)
//		{
//			$sMake = $this->get('sMake');
//			$iYear = $this->get('iYear');
//		}
//		else
//		{
//			$this->html('#dvs_select_box_make_text', $sMake);
//			$this->val('#dvs_video_select_make_input', $sMake);
//		}
//		
//		if (!$sMake)
//		{
//			return false;
//		}
//
//		$sSelectOptions = '';
//
//		$aModels = Phpfox::getService('dvs.video')->getVideoSelect($iYear, $sMake, '', true);
//
//		$sBrowser = Phpfox::getService('dvs')->getBrowser();
//
//		if ($sBrowser == 'mobile' || $sBrowser == 'ipad')
//		{
//			$sSelect = '<select id="dvs_video_select_model" class="dvs_select" onchange="$.ajaxCall(\'dvs.videoSelect\', \'sModel=\' + this.value + \'&amp;sMake=' . $sMake . '&amp;iYear=\' + $(\'#dvs_video_select_year\').val() + \'&amp;sPlaylistBorder=\' + $(\'#contact_dvs_id\').val() + \'&amp;sPlaylistBorder=\' + $(\'#dvs_playlist_border_color\').val());"><option value="">' . Phpfox::getPhrase('dvs.select_model') . '</option>';
//			foreach ($aModels as $aModel)
//			{
//				$sSelect .= '<option class="dvs_year_reset" value="' . $aModel['model'] . '">' . $aModel['year'] . ' ' . $aModel['model'] . (Phpfox::getParam('dvs.javascript_debug_mode') ? ' (' . $aModel['video_type'] . ')' : '') . '</option>';
//			}
//			$sSelect .= '</select>';
//		}
//		else
//		{
//			foreach ($aModels as $aModel)
//			{
//				$sSelectOptions .= '<li class="dvs_year_reset"><a href="#" class="video_type_' . $aModel['video_type'] . '" onclick="$(\'#dvs_select_box_model_text\').html(\'' . $aModel['year'] . ' ' . $aModel['model'] . '\');$.ajaxCall(\'dvs.videoSelect\', \'sModel=' . $aModel['model'] . '&amp;iYear=' . $aModel['year'] . '&amp;sMake=' . $aModel['make'] . '&amp;iDvsId=\' + $(\'#contact_dvs_id\').val() + \'&amp;sPlaylistBorder=\' + $(\'#dvs_playlist_border_color\').val());">' . $aModel['year'] . ' ' . $aModel['model'] . (Phpfox::getParam('dvs.javascript_debug_mode') ? ' (' . $aModel['video_type'] . ')' : '') . '</a></li>';
//			}
//
//			$sSelect = '<div class="dvs_select_box_anchor" data-dropdown="#dvs_video_select_model" data-vertical-offset="15"><div class="dvs_select_box_anchor_text" id="dvs_select_box_model_text">' . Phpfox::getPhrase('dvs.select_model') . '</div></div>';
//			$sSelect .= '<div class="dropdown dvs_select_options_container dropdown-anchor-right dropdown-relative" id="dvs_video_select_model">';
//			$sSelect .= '<ul class="dvs_year_reset dropdown-menu">';
//
//			if ($sSelectOptions)
//			{
//				$sSelect .= $sSelectOptions;
//			}
//			else
//			{
//				$sSelect .= '<li class="dvs_year_reset"><a href="#">No Models Found</a></li>';
//			}
//
//			$sSelect .= '</ul>';
//			$sSelect .= '</div>';
//		}
//
//		$this->html('#dvs_vehicle_select_model_container', $sSelect);
	}

	public function getFeaturedModels()
	{
		$aYears = Phpfox::getParam('dvs.new_years');
		$sMakes = Phpfox::getLib('request')->get('aMakes');
		$aMakes = explode(',', $sMakes);

		$aPlayerModels = array();

		foreach ($aYears as $iYear)
		{
			foreach ($aMakes as $sMake)
			{
				$aModels = Phpfox::getService('dvs.video')->getModels($iYear, $sMake);

				$aPlayerModels = array_merge($aPlayerModels, $aModels);
			}
		}


		$sSelect = '<select id="dvs_video_select_model"><option value="">' . Phpfox::getPhrase('dvs.select_model') . '</option>';
		foreach ($aPlayerModels as $aModel)
		{
			$sSelect .= '<option value="' . $aModel['year'] . ',' . $aModel['make'] . ',' . $aModel['model'] . '">' . $aModel['year'] . ' ' . $aModel['make'] . ' ' . $aModel['model'] . '</option>';
		}
		$sSelect .= '</select>';

		$this->html('#featured_model', $sSelect);
	}

	/**
	 * Select a video based on the year, make, and model selected in the video select drop down box
	 * Build a new playlist and set video_select_playlist, call enableVideoSelectCarousel
	 */
	public function videoSelect()
	{
		$iYear = $this->get('iYear');
		$sMake = $this->get('sMake');
		$sModel = $this->get('sModel');
		$sPlaylistBorder = $this->get('sPlaylistBorder');
		$iDvsId = $this->get('iDvsId');
		$aDvs = Phpfox::getService('dvs')->get($iDvsId);
		Phpfox::getService('dvs.video')->setDvs($iDvsId);

		$aVideos = array();
		$aVideos = Phpfox::getService('dvs.video')->getVideoSelect($iYear, $sMake, $sModel);
		$aVideos = array_merge($aVideos, Phpfox::getService('dvs.video')->getRelated($aVideos[0]));

		//Build media id js array
		$this->call('aVideoSelectMediaIds = [];');
		foreach ($aVideos as $iKey => $aVideo)
		{
			$this->call('aVideoSelectMediaIds[' . $iKey . '] = "' . $aVideo['id'] . '";');
		}

		$sBrowser = Phpfox::getService('dvs')->getBrowser();

		//Build playlist html and set
		Phpfox::getLib('setting')->setParam(array('dvs.video_url_image' => Phpfox::getParam('core.url_file') . 'brightcove/'));
		$sPlaylistHtml = '<ul>';

		foreach ($aVideos as $iKey => $aVideo)
		{
			$sThumbnailImageHtml = Phpfox::getLib('image.helper')->display(array(
				'path' => 'dvs.video_url_image',
				'file' => $aVideo['thumbnail_image'],
				'max_width' => 145,
				'max_height' => 82));

			$sPlaylistHtml .= '<li>' .
				'<a href="#" onclick="' . ($sBrowser == 'mobile' || $sBrowser == 'ipad' ? 'modVid.loadVideoByID' : 'modCon.getMediaAsynch') . '(aMediaIds[' . $iKey . ']);return false;">' .
				
				$sThumbnailImageHtml . '<p>' . $aVideo['year'] . ' ' . $aVideo['model'] . '</p></a>' .
				'</li>';
		}

		$sPlaylistHtml .= '</ul>';

		$this->html('#overview_playlist', $sPlaylistHtml);
		//Enable playlist for jCarousel

		if ($sBrowser != 'mobile')
		{
			$this->call('enableVideoSelectCarousel();');
		}

		//Switch to Video Select
		$this->call('watchVideoSelect(aVideoSelectMediaIds);');
	}

	public function removeTeamMember()
	{
		$iSalesTeamId = $this->get('salesteam_id');

		Phpfox::getService('dvs.salesteam.process')->remove($iSalesTeamId);

		$this->hide('#sales_team_member_' . $iSalesTeamId);
	}

	public function showGetPriceForm()
	{
		Phpfox::getBlock('dvs.get-price', array('iDvsId' => $this->get('iDvsId'), 'sRefId' => $this->get('sRefId')));
	}
	
	public function emailForm()
	{
		Phpfox::getBlock('dvs.share-email', array('iDvsId' => $this->get('iDvsId'), 'sRefId' => $this->get('sRefId')));
	}

}
?>
