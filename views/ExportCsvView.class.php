<?php
class ao_ExportCsvView extends f_view_BaseView
{

	public function _execute($context, $request)
	{
		$companiesFolder = $request->getAttribute('companiesFolder');

		$companies = ao_CompanyService::getInstance()->createQuery()
			->find();
		$model = f_persistentdocument_PersistentDocumentModel::getInstance('ao', 'company');
		$forbiddenProperties = array(
			'publicationstatus', 'creationdate', 'modificationdate', 'author',
			'lang', 'modelversion', 'documentversion', 'startpublicationdate',
			'endpublicationdate', 'frontendUser', 'parentFolder', 'theme',
			'mainBusiness', 'otherBusiness'
			);
		foreach ($model->getPropertiesNames() as $propertyName)
		{
			if ( ! in_array($propertyName, $forbiddenProperties) )
			{
				$fieldNames[$propertyName] = f_Locale::translate('&modules.ao.document.company.'.$propertyName.';');
			}
		}

		foreach ($companies as $company)
		{
			$tmp = DocumentHelper::getPropertiesOf($company);
			foreach ($forbiddenProperties as $name)
			{
				unset($tmp[$name]);
			}
			$data[] = $tmp;
		}
		$fileName = "export_entreprises_".date('Ymd_His').'.txt';
		$options = new f_util_CSVUtils_export_options();
		$csv = f_util_CSVUtils::export($fieldNames, $data, $options);
		header("Content-type: text/plain; charset=UTF-8");
		header('Content-length: '.strlen($csv));
		header('Content-disposition: attachment; filename="'.$fileName.'"');
		echo $csv;
		exit;
	}
}