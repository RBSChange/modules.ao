<?php
class ao_AOListener
{
	public function onPersistentDocumentPublished($sender, $params)
	{
		if ($params['document'] instanceof ao_persistentdocument_ao && $params['oldPublicationStatus'] != 'PUBLICATED')
		{
			ao_AoService::getInstance()->onAOPublished($params['document']);
		}
	}

	public function onMediaDownloaded($sender, $params)
	{
		ao_AoService::getInstance()->onMediaDownloaded($params['document'], $params['mediaId']);
	}
}