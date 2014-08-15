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
class Dvs_Service_Video_Video extends Phpfox_Service {

	public $aDvs = array();

	public function __construct()
	{
		$this->_tVideos = Phpfox::getT('ko_brightcove');
	}


	/*
	 * Set $this->aDvs
	 */

	public function setDvs($iDvsId)
	{
		$this->aDvs = Phpfox::getService('dvs')->get($iDvsId);
	}


	/**
	 * Get a list of all available makes
	 *
	 * @return array
	 */
	public function getMakes()
	{
		$aMakes = $this->database()
			->select('DISTINCT make')
			->from($this->_tVideos)
			->order('make')
			->execute('getRows');

		return $aMakes;
	}


	/**
	 * Get a video based on the primary id, video title url, or a year make and model.
	 *
	 * @param type $mVideoId, reference id or video title url
	 * @param type $bUseTitle, use title url or otherwise use the ko_id, the primary key
	 * @param type $iYear
	 * @param type $sMake
	 * @param type $sModel
	 * @return type
	 */
	public function get($mVideoId, $bUseTitle = false, $iYear = '', $sMake = '', $sModel = '')
	{
		$aWhere = array();
		if ($bUseTitle)
		{
			if (!empty($this->aDvs))
			{
				// If this is for a DVS, use phrase overrides for video title URLs
				$aReplace = array(
					'overview',
					'used-car-review',
					'test-drive'
				);

				$aFind = array(
					($this->aDvs['1onone_override'] ? $this->aDvs['1onone_override'] : (Phpfox::getParam('dvs.1onone_video_url_replacement') ? Phpfox::getParam('dvs.1onone_video_url_replacement') : 'overview')),
					($this->aDvs['new2u_override'] ? $this->aDvs['new2u_override'] : (Phpfox::getParam('dvs.new2u_video_url_replacement') ? Phpfox::getParam('dvs.new2u_video_url_replacement') : 'used-car-review')),
					($this->aDvs['top200_override'] ? $this->aDvs['top200_override'] : (Phpfox::getParam('dvs.top200_video_url_replacement') ? Phpfox::getParam('dvs.top200_video_url_replacement') : 'test-drive'))
				);

				$mVideoId = str_replace($aFind, $aReplace, $mVideoId);
				// Strip out extra dealer information
				if (strrpos($mVideoId, 'overview') !== false) $mVideoId = substr($mVideoId, 0, (strrpos($mVideoId, 'overview') + 8));
				if (strrpos($mVideoId, 'used-car-review') !== false) $mVideoId = substr($mVideoId, 0, (strrpos($mVideoId, 'used-car-review') + 15));
				if (strrpos($mVideoId, 'test-drive') !== false) $mVideoId = substr($mVideoId, 0, (strrpos($mVideoId, 'test-drive') + 10));
			}

			$aWhere[] = 'video_title_url = "' . $this->preParse()->clean($mVideoId) . '"';
		}
		else if ($iYear || $sMake || $sModel)
		{
			$iYear = (int) $iYear;
			$sMake = $this->preParse()->clean($sMake);
			$sModel = $this->preParse()->clean($sModel);

			$aWhere[] = 'year LIKE "%' . $iYear . '%"';
			$aWhere[] = 'AND make LIKE "%' . $sMake . '%"';
			$aWhere[] = 'AND model LIKE "%' . $sModel . '%"';
		}
		else
		{
			$aWhere[] = 'referenceId = "' . $this->preParse()->clean($mVideoId) . '"';
		}

		$aVideo = $this->database()
			->select('*')
			->from($this->_tVideos)
			->where($aWhere)
			->execute('getRow');

		return $this->prepareVideos($aVideo, true);
	}


	/**
	 * Remove duplicates videos
	 *
	 * @param type $aVideos
	 * @return type
	 */
	public function removeDupes($aVideos)
	{
		$aReturn = array();
		$aIds = array();

		foreach ($aVideos as $aVideo)
		{
			if (isset($aVideo['ko_id']))
			{
				if (!isset($aIds[$aVideo['ko_id']]))
				{
					$aReturn[] = $aVideo;
					$aIds[$aVideo['ko_id']] = true;
				}
			}
		}

		unset($aVideos);
		return $aReturn;
	}


	/**
	 * Get Overview videos based on AdminCP settings
	 *
	 * @param type $iDvsId
	 * @return type
	 */
	public function getOverviewVideos($iDvsId, $aSelectedMakes = null)
	{
		if ($aSelectedMakes)
		{
			$aMakes = array();
			foreach ($aSelectedMakes as $sMake)
			{
				$aMakes[]['make'] = $sMake;
			}
		}
		else
		{
			$aPlayer = Phpfox::getService('dvs.player')->get($iDvsId);
			$aMakes = $aPlayer['makes'];
		}

		$aFilters = array();

		$aAllowedYears = Phpfox::getParam('dvs.vf_overview_allowed_years');
		if ($aAllowedYears)
		{
			$iLoops = 0;
			$sWhere = '(';
			foreach ($aAllowedYears as $sYear)
			{
				$iLoops++;
				$sWhere .= 'year = "' . $sYear . '"';
				if (count($aAllowedYears) > $iLoops)
				{
					$sWhere .= ' OR ';
				}
				else
				{
					$sWhere .= ')';
				}
			}
			$aFilters[] = $sWhere;
		}

		if (!Phpfox::getParam('dvs.vf_overview_allow_1onone'))
		{
			$aFilters[] = 'AND referenceId NOT LIKE "1onONE%"';
		}

		if (!Phpfox::getParam('dvs.vf_overview_allow_top200'))
		{
			$aFilters[] = 'AND referenceId NOT LIKE "Top200%"';
		}

		if (!Phpfox::getParam('dvs.vf_overview_allow_pov'))
		{
			$aFilters[] = 'AND referenceId NOT LIKE "POV%"';
		}

		if (!Phpfox::getParam('dvs.vf_overview_allow_new2u'))
		{
			$aFilters[] = 'AND referenceId NOT LIKE "New2U%"';
		}

		$aVideos = array();

		foreach ($aMakes as $aMake)
		{
			$aWhere = array_merge($aFilters, array('AND make = "' . $this->preParse()->clean($aMake['make']) . '"'));

			$aRows = $this->database()
				->select('*')
				->from($this->_tVideos)
				->order('year DESC')
				->where($aWhere)
				->limit(Phpfox::getParam('dvs.vf_overview_max_videos_per_make'))
				->execute('getRows');

			if ($aRows)
			{
				$aVideos[] = $aRows;
			}
		}

		$aOverviewVideos = $this->limitVideos($aVideos, Phpfox::getParam('dvs.vf_overview_max_videos'));

		$aOverviewVideos = $this->sortVideos($aOverviewVideos, Phpfox::getParam('dvs.vf_overview_round_robin'));

		return $this->prepareVideos($aOverviewVideos);
	}


	/**
	 * Returns an array of videos based on optional search parameters
	 *
	 * @param int $iYear
	 * @param string $sMake
	 * @param string $sModel
	 * @param int $iLimit
	 * @return array
	 */
	public function getVideoSelect($iYear = 0, $sMake = '', $sModel = '', $bIgnoreLimit = false)
	{
		$aWhere = array();

		$aAllowedYears = Phpfox::getParam('dvs.vf_video_select_allowed_years');
		if ($aAllowedYears)
		{
			$iLoops = 0;
			$sWhere = '(';
			foreach ($aAllowedYears as $sYear)
			{
				$iLoops++;
				$sWhere .= 'year = "' . $sYear . '"';
				if (count($aAllowedYears) > $iLoops)
				{
					$sWhere .= ' OR ';
				}
				else
				{
					$sWhere .= ')';
				}
			}
			$aWhere[] = $sWhere;
		}

		if (!Phpfox::getParam('dvs.vf_video_select_allow_1onone'))
		{
			$aWhere[] = 'AND referenceId NOT LIKE "1onONE%"';
		}

		if (!Phpfox::getParam('dvs.vf_video_select_allow_top200'))
		{
			$aWhere[] = 'AND referenceId NOT LIKE "Top200%"';
		}

		if (!Phpfox::getParam('dvs.vf_video_select_allow_pov'))
		{
			$aWhere[] = 'AND referenceId NOT LIKE "POV%"';
		}

		if (!Phpfox::getParam('dvs.vf_video_select_allow_new2u'))
		{
			$aWhere[] = 'AND referenceId NOT LIKE "New2U%"';
		}

		$aVideos = array();

		if ($iYear)
		{
			$aWhere[] = 'AND year = "' . $this->preParse()->clean($iYear) . '"';
		}
		if ($sMake)
		{
			$aWhere[] = 'AND make = "' . $this->preParse()->clean($sMake) . '"';
		}
		if ($sModel)
		{
			$aWhere[] = 'AND model = "' . $this->preParse()->clean($sModel) . '"';
		}

		if (!$bIgnoreLimit)
		{
			$this->database()->limit(Phpfox::getParam('dvs.vf_video_select_max_videos'));
		}

		$aVideos = $this->database()
			->select('*')
			->from($this->_tVideos)
			->order('model')
			->where($aWhere)
			->execute('getRows');

		return $this->prepareVideos($aVideos);
	}


	/**
	 * Return videos with a similar year, make, model, and body style
	 *
	 * @param type $aVideo
	 * @return type
	 */
	public function getRelated($aVideo)
	{
		$aWhere = array();

		$aAllowedYears = Phpfox::getParam('dvs.vf_related_allowed_years');
		if ($aAllowedYears)
		{
			$iLoops = 0;
			$sWhere = '(';
			foreach ($aAllowedYears as $sYear)
			{
				$iLoops++;
				$sWhere .= 'year = "' . $sYear . '"';
				if (count($aAllowedYears) > $iLoops)
				{
					$sWhere .= ' OR ';
				}
				else
				{
					$sWhere .= ')';
				}
			}
			$aWhere[] = $sWhere;
		}

		if (!Phpfox::getParam('dvs.vf_related_allow_1onone'))
		{
			$aWhere[] = 'AND referenceId NOT LIKE "1onONE%"';
		}

		if (!Phpfox::getParam('dvs.vf_related_allow_top200'))
		{
			$aWhere[] = 'AND referenceId NOT LIKE "Top200%"';
		}

		if (!Phpfox::getParam('dvs.vf_related_allow_pov'))
		{
			$aWhere[] = 'AND referenceId NOT LIKE "POV%"';
		}

		if (!Phpfox::getParam('dvs.vf_related_allow_new2u'))
		{
			$aWhere[] = 'AND referenceId NOT LIKE "New2U%"';
		}

		$aWhere[] = 'AND referenceId != "' . $aVideo['referenceId'] . '"';

		if (Phpfox::getParam('dvs.vf_related_force_same_year'))
		{
			$aWhere[] = 'AND year = "' . $aVideo['year'] . '"';
		}

		if (Phpfox::getParam('dvs.vf_related_force_same_make'))
		{
			$aWhere[] = 'AND make = "' . $aVideo['make'] . '"';
		}

		if (Phpfox::getParam('dvs.vf_related_force_same_model'))
		{
			$aWhere[] = 'AND model = "' . $aVideo['model'] . '"';
		}

		if (Phpfox::getParam('dvs.vf_related_force_same_body_style'))
		{
			$aWhere[] = 'AND bodyStyle = "' . $aVideo['bodyStyle'] . '"';
		}

		$aVideos = $this->database()
			->select('*')
			->from($this->_tVideos)
			->order('year DESC')
			->where($aWhere)
			->execute('getRows');

		// Sort videos by make
		$aRelated = array();
		$aRelatedMakes = array();
		foreach ($aVideos as $aVideo)
		{
			$bMakeExists = false;
			foreach ($aRelatedMakes as $iMakeKey => $sMake)
			{
				if ($sMake == $aVideo['make'])
				{
					$bMakeExists = true;
					$iMake = $iMakeKey;
				}
			}

			if (!$bMakeExists)
			{
				$aRelatedMakes[] = $aVideo['make'];
				$aRelated[] = array($aVideo);
			}
			else
			{
				if (count($aRelated[$iMake]) < Phpfox::getParam('dvs.vf_related_max_videos_per_make'))
				{
					$aRelated[$iMake][] = $aVideo;
				}
			}
		}

		$aRelated = $this->limitVideos($aRelated, Phpfox::getParam('dvs.vf_related_max_videos'));
		return $this->sortVideos($aRelated, Phpfox::getParam('dvs.vf_related_round_robin'));
	}


	/**
	 * Limits videos based on the total amount of videos and total number of makes
	 *
	 * @param type $aVideos
	 * @param type $ iMaxVideos
	 * @return type
	 */
	public function limitVideos($aVideos, $iMaxVideos)
	{
		$iTotalVideos = 0;
		foreach ($aVideos as $aMake)
		{
			$iTotalVideos += count($aMake);
		}

		if ($iTotalVideos > $iMaxVideos)
		{
			$iTotalMakes = count($aVideos);
			foreach ($aVideos as $iMakeKey => $aMake)
			{
				foreach ($aMake as $iVideoKey => $aVideo)
				{
					if ($iVideoKey > floor($iMaxVideos / $iTotalMakes))
					{
						if ($iTotalVideos > $iMaxVideos)
						{
							unset($aVideos[$iMakeKey][$iVideoKey]);
							$iTotalVideos--;
						}
					}
				}
			}
		}
		return $aVideos;
	}


	/**
	 * Sorts videos based on round robin setting
	 *
	 * @param type $aVideos
	 * @param type $bRoundRobin
	 * @return type
	 */
	public function sortVideos($aVideos, $bRoundRobin)
	{
		//  Sort the videos
		$aReturnVideos = array();

		if ($bRoundRobin)
		{
			$iLoops = 0;
			do
			{
				foreach ($aVideos as $iMakeKey => $aMake)
				{
					// Make may contain no videos
					if (!$aMake)
					{
						unset($aVideos[$iMakeKey]);
						continue;
					}

					if (isset($aMake[$iLoops]))
					{
						$aReturnVideos[] = $aMake[$iLoops];
						unset($aVideos[$iMakeKey][$iLoops]);

						if (count($aVideos[$iMakeKey]) == 0)
						{
							unset($aVideos[$iMakeKey]);
						}
					}
				}
				$iLoops++;
			}
			while (count($aVideos));
		}
		else
		{
			foreach ($aVideos as $aMake)
			{
				foreach ($aMake as $aVideo)
				{
					$aReturnVideos[] = $aVideo;
				}
			}
		}

		return $aReturnVideos;
	}


	/**
	 * Get all available models for a specific year and make
	 *
	 * @param type $iYear
	 * @param type $sMake
	 * @return type
	 */
	public function getModels($iYear, $sMake)
	{
		$iYear = (int) $iYear;
		$sMake = $this->preParse()->clean($sMake);

		return $this->database()
				->select('model, year, make')
				->from($this->_tVideos)
				->where('tags LIKE "%' . $iYear . '%" AND make = "' . $sMake . '"')
				->group('model')
				->order('model')
				->execute('getRows');
	}


	/**
	 * Return an array contataining only years with cars associated with them
	 *
	 * @param array years
	 */
	public function getValidVSYears($aMakes)
	{
		$aAllowedYears = Phpfox::getParam('dvs.vf_video_select_allowed_years');
		foreach ($aAllowedYears as $iYearKey => $iYear)
		{
			$bHasData = false;
			foreach ($aMakes as $iMakeKey => $aMake)
			{
				if ($this->getVideoSelect($iYear, $aMake['make'], '', 1))
				{
					$bHasData = true;
					break 1;
				}
			}
			if (!$bHasData)
			{
				unset($aAllowedYears[$iYearKey]);
			}
		}


		$aAllowedYears = array_values($aAllowedYears);

		return $aAllowedYears;
	}


	/**
	 * Returns makes for the video select box
	 *
	 * @param type $iYear
	 * @return array of makes
	 */
	public function getValidVSMakes($iYear, $aMakes)
	{
		$aWhere = array();
		if(!empty($iYear)){
			$aWhere[] = 'year = ' . (int) $iYear;
		}

		$sPlayerMakes = ($aWhere?'AND ':'').'(';
		foreach ($aMakes as $iKey => $aMake)
		{
			$sPlayerMakes .= 'make LIKE "' . $aMake['make'] . '"';
			if ($iKey + 1 < count($aMakes))
			{
				$sPlayerMakes .= ' OR ';
			}
		}
		$aWhere[] = $sPlayerMakes . ')';

		if (!Phpfox::getParam('dvs.vf_video_select_allow_1onone'))
		{
			$aWhere[] = 'AND referenceId NOT LIKE "1onONE%"';
		}

		if (!Phpfox::getParam('dvs.vf_video_select_allow_top200'))
		{
			$aWhere[] = 'AND referenceId NOT LIKE "Top200%"';
		}

		if (!Phpfox::getParam('dvs.vf_video_select_allow_pov'))
		{
			$aWhere[] = 'AND referenceId NOT LIKE "POV%"';
		}

		if (!Phpfox::getParam('dvs.vf_video_select_allow_new2u'))
		{
			$aWhere[] = 'AND referenceId NOT LIKE "New2U%"';
		}

		$aMakes = $this->database()
			->select('DISTINCT make')
			->from($this->_tVideos)
			->order('make')
			->where($aWhere)
			->execute('getRows');

		return $aMakes;
	}


	/**
	 * Sets the video type and if iDvsId is supplied, resets the title_url
	 *
	 * @param type $aVideos
	 * @param type $iDvsId
	 * @return type
	 */
	public function prepareVideos($aVideos, $bSingleVideo = false)
	{
		if (empty($aVideos))
		{
			return array();
		}

		if ($bSingleVideo)
		{
			$aVideo = $aVideos;
			$aVideos = array($aVideo);
			unset($aVideo);
		}

		foreach ($aVideos as $iKey => $aVideo)
		{
			$aVideos[$iKey]['video_type'] = substr($aVideo['referenceId'], 0, strpos($aVideo['referenceId'], '_'));

			if (!empty($this->aDvs))
			{
				// If this is for a DVS, use phrase overrides for video title URLs
				$aFind = array(
					'overview',
					'used-car-review',
					'test-drive'
				);

				$aReplace = array(
					($this->aDvs['1onone_override'] ? $this->aDvs['1onone_override'] : (Phpfox::getParam('dvs.1onone_video_url_replacement') ? Phpfox::getParam('dvs.1onone_video_url_replacement') : 'overview')),
					($this->aDvs['new2u_override'] ? $this->aDvs['new2u_override'] : (Phpfox::getParam('dvs.new2u_video_url_replacement') ? Phpfox::getParam('dvs.new2u_video_url_replacement') : 'used-car-review')),
					($this->aDvs['top200_override'] ? $this->aDvs['top200_override'] : (Phpfox::getParam('dvs.top200_video_url_replacement') ? Phpfox::getParam('dvs.top200_video_url_replacement') : 'test-drive'))
				);

				$aVideos[$iKey]['video_title_url'] = str_replace($aFind, $aReplace, $aVideo['video_title_url']);

				if (Phpfox::getParam('dvs.dvs_info_video_url_replacement'))
				{
					$aVideos[$iKey]['video_title_url'] .= '-' . $this->aDvs['title_url'] . '-' . strtolower(str_replace(' ', '-', $this->aDvs['city'])) . '-' . strtolower(str_replace(' ', '-', $this->aDvs['state_string']));
				}
			}
		}

		return ($bSingleVideo ? $aVideos[0] : $aVideos);
	}

    public function getShareVideos($iDvsId, $iYear, $aSelectedMakes = array(), $sModel = '') {
        if (is_array($aSelectedMakes)) {
            $aMakes = $aSelectedMakes;
        } elseif($aSelectedMakes) {
            $aMakes = array();
            $aMakes[]['make'] = $aSelectedMakes;
        } else {
            $aPlayer = Phpfox::getService('dvs.player')->get($iDvsId);
            $aMakes = $aPlayer['makes'];
        }

        $aFilters = array();

        $aFilters[] = 'year = ' . (int)$iYear;

        if ($sModel) {
            $aFilters[] = ' AND model = \'' . $sModel . '\'';
        }

        if (!Phpfox::getParam('dvs.vf_overview_allow_1onone'))
        {
            $aFilters[] = 'AND referenceId NOT LIKE "1onONE%"';
        }

        if (!Phpfox::getParam('dvs.vf_overview_allow_top200'))
        {
            $aFilters[] = 'AND referenceId NOT LIKE "Top200%"';
        }

        if (!Phpfox::getParam('dvs.vf_overview_allow_pov'))
        {
            $aFilters[] = 'AND referenceId NOT LIKE "POV%"';
        }

        if (!Phpfox::getParam('dvs.vf_overview_allow_new2u'))
        {
            $aFilters[] = 'AND referenceId NOT LIKE "New2U%"';
        }

        $aVideos = array();

        foreach ($aMakes as $aMake)
        {
            $aWhere = array_merge($aFilters, array('AND make = "' . $this->preParse()->clean($aMake['make']) . '"'));

            $aRows = $this->database()
                ->select('*')
                ->from($this->_tVideos)
                ->order('year DESC')
                ->where($aWhere)
                ->limit(Phpfox::getParam('dvs.vf_overview_max_videos_per_make'))
                ->execute('getRows');

            if ($aRows)
            {
                $aVideos[] = $aRows;
            }
        }

        $aOverviewVideos = $this->limitVideos($aVideos, Phpfox::getParam('dvs.vf_overview_max_videos'));

        $aOverviewVideos = $this->sortVideos($aOverviewVideos, Phpfox::getParam('dvs.vf_overview_round_robin'));

        $iUserId = Phpfox::getUserId();
        foreach ($aOverviewVideos as $iKey => $aVideo) {
            $aOverviewVideos[$iKey]['shorturl'] = Phpfox::getService('dvs.shorturl')->generate($iDvsId, $aVideo['referenceId'], 'embed', $iUserId, 1);

            if (Phpfox::getParam('dvs.enable_subdomain_mode')){
                $aOverviewVideos[$iKey]['entire_shorturl'] = Phpfox::getLib('url')->makeUrl('') . $aOverviewVideos[$iKey]['shorturl'];
            }else{
                $aOverviewVideos[$iKey]['entire_shorturl'] = Phpfox::getLib('url')->makeUrl('dvs') . $aOverviewVideos[$iKey]['shorturl'];
            }
        }

        return $this->prepareVideos($aOverviewVideos);

    }

    public function getRelatedVideo($aVideo, $iDvsId) {
        $sWhere = '1';
        $aPlayer = Phpfox::getService('dvs.player')->get($iDvsId);
        $aMakes = array();
        foreach($aPlayer['makes'] as $aMake) {
            $aMakes[] = '\'' . $aMake['make'] . '\'';
        }
        $sWhere .= ' AND make IN (' . implode(',' , $aMakes) . ')';


        if(Phpfox::getParam('dvs.vf_related_force_same_year')) {
            $sWhere .= ' AND year = ' . (int)$aVideo['year'];
        }

        if(Phpfox::getParam('dvs.vf_related_force_same_make')) {
            $sWhere .= ' AND make = \'' . $aVideo['make'] . '\'';
        }

        if(Phpfox::getParam('dvs.vf_related_force_same_model')) {
            $sWhere .= ' AND model = \'' . $aVideo['model'] . '\'';
        }

        if(Phpfox::getParam('dvs.vf_related_force_same_body_style')) {
            $sWhere .= ' AND bodyStyle = \'' . $aVideo['bodyStyle'] . '\'';
        }

        $aRows = $this->database()
            ->select('*')
            ->from($this->_tVideos)
            ->order('year DESC')
            ->where($sWhere)
            ->limit(Phpfox::getParam('dvs.vf_overview_max_videos_per_make'))
            ->execute('getRows');

        return $aRows;
    }
}

?>