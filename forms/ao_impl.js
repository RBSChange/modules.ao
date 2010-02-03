function onInit()
{
	var handler =
	{
		xbl : this,
		handleEvent : function(e)
		{
			this.xbl.updateAwardNoticeFields();
		}
	}
	this.getFieldByName('awardNotice').addEventListener('change', handler, false);
}

function updateAwardNoticeFields()
{
	var awardNotice = this.getFieldByName('awardNotice').value;
	this.getBoxForField(this.getFieldByName('awardNoticePublicationDate')).hidden = ! (awardNotice && awardNotice.length);
	this.getBoxForField(this.getFieldByName('awardNoticePublicationTime')).hidden = ! (awardNotice && awardNotice.length);
}

function onLoad()
{
	this.updateAwardNoticeFields();
}

function getBoxForField(field)
{
	var p = field.parentNode;
	while (p && p.tagName != 'row' && p.tagName != 'xul:row')
	{
		p = p.parentNode;
	}
	return p ? p : field;
}

function onBeforeSave()
{
	var awardNotice = this.getFieldByName('awardNotice').value;
	var awardNoticeDate = this.getFieldByName('awardNoticePublicationDate').value;
	if (awardNotice && awardNotice.length && ! awardNoticeDate)
	{
		alert("&modules.ao.bo.general.Award-notice-missing-date-warning;");
		return false;
	}
	return true;
}