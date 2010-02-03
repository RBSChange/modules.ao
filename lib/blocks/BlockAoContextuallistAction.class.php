<?php
class ao_BlockAoContextuallistAction extends block_BlockAction
{
	/**
	 * @param block_BlockContext $context
	 * @param block_BlockRequest $request
	 * @return String view name
	 */
	public function execute($context, $request)
	{
		// Get the parent topic
		$ancestor = $context->getAncestors();
        $topicId = f_util_ArrayUtils::lastElement($ancestor);

        $container = DocumentHelper::getDocumentInstance($topicId);
		$this->setParameter('container', $container);

		// Get ordered AO according to the block's parameters.
		$sortByParam = $this->getParameter('sortby');
		$order = $sortByParam == 'reference' ? Order::asc('reference') : Order::desc('emissionDate');
		$selectedTheme = $request->getParameter('theme', null);
		if ( ! $selectedTheme )
		{
			// Force $selectedTheme to null if empty because ao_AoService::getOrdered() checks with is_null().
			$selectedTheme = null;
		}

		$awardNoticeArray = ao_AoService::getInstance()->getAwardNoticeArray($selectedTheme, $container);
		$aoArray = ao_AoService::getInstance()->getOrdered($selectedTheme, $container, $order);

		// View AO or awards?
		if ($request->getParameter('awards'))
		{
			$otherCount = count($aoArray);
			$aoArray = $awardNoticeArray;
			$this->setParameter(
				'viewAoURL',
				LinkHelper::getUrl(DocumentHelper::getDocumentInstance($context->getId()), null, array(
					'aoParam' => array('theme' => $selectedTheme)))
				);
			$this->setParameter('viewAwards', true);
		}
		else
		{
			$otherCount = count($awardNoticeArray);
			$this->setParameter(
				'viewAwardNoticesURL',
				LinkHelper::getUrl(DocumentHelper::getDocumentInstance($context->getId()), null, array(
					'aoParam' => array('awards' => 'true', 'theme' => $selectedTheme)))
				);
			$this->setParameter('viewAwards', false);
		}
		$this->setParameter('otherCount', $otherCount);

		// Group by themes if needed.
		if ( ! $selectedTheme && $this->getParameter('groupbythemes') )
		{
			$aoByTheme = array();
			foreach ($aoArray as $ao)
			{
				$theme = $ao->getTheme();
				$themeLabel = is_null($theme) ? '-' : $theme->getLabel();
				$aoByTheme[$themeLabel][] = $ao;
			}
			ksort($aoByTheme);
			$this->setParameter('aoListByTheme', $aoByTheme);
			$this->setParameter('groupByThemes', true);
		}
		else
		{
			$this->setParameter('aoList', $aoArray);
			$this->setParameter('groupByThemes', false);
		}

		// Build parameter for the Themes listbox.
		$themesParameter = array();
		foreach (ao_ThemeService::getInstance()->getAffectedThemes() as $theme)
		{
			$themesParameter[] = array(
				'id' => $theme->getId(),
				'label' => $theme->getLabel(),
				'selected' => $selectedTheme == $theme->getId()
				);
		}
		$this->setParameter('selectedTheme', $selectedTheme);
		$this->setParameter('themes', $themesParameter);

		return block_BlockView::SUCCESS;
	}
}