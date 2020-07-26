CRM.$(function($) {
  $("body").prepend('<div class="loader-apicall"></div>');
  $('.loader-apicall').hide();

  $('#pricesetTotal').before('<div class=\"slotmessage\"></div>');
  $('#priceset [price]').on('change', function() {
    slotmessage(this);
  });
  cj("#priceset [price]").each(function () {
    slotmessage(this);
  });

  function slotmessage(object) {
    let priceName, value, eleId;
    let elementtype = $(object).prop('type');
    let priceFieldIDs = CRM.vars.slotavailabilitymsg.priceFieldIds;
    priceName = $(object).prop('name');

    eleId = priceName.replace('[', '_');
    eleId = eleId.replace(']', '');
    $('div.slotmessage #price' + eleId).remove();

    switch(elementtype) {
      case 'checkbox':
        if ($(object).prop('checked')) {
          eval('var option = ' + $(object).attr('price'));
          value = option[0];
        }
        break;

      case 'radio':
        value = $('input[name=' + priceName + ']:radio:checked').val();
        break;

      case 'text':
        value = $(object).val();
        break;

      case 'select-one':
        value = $(object).val();
        break;
    }

    if (value > 0 && priceFieldIDs[value] !== undefined) {
      printMessage(priceFieldIDs[value], priceName, object, value);
    }
  }

  function printMessage(priceLabel, priceName, priceElement, priceFieldId) {
    $('.loader-apicall').show();
    $('body').addClass('loader-body');

    CRM.api3('Slotavailabilitymsg', 'getpartcount', {
      "price_id": priceFieldId,
      "event_id": CRM.vars.slotavailabilitymsg.eventId
    }).then(function(result) {
      // do something with result
      $('.loader-apicall').hide();
      $('body').removeClass('loader-body');
      let message = CRM.vars.slotavailabilitymsg.message;
      let optionLabel = $(priceElement).closest('.crm-section').find('div.label label').text();

      message = message.replace('%priLabel', optionLabel.replace('*', ''));
      message = message.replace('%priOpLabel', priceLabel);
      message = message.replace('%partCount', result.result);

      priceName = priceName.replace('[', '_');
      priceName = priceName.replace(']', '');
      $('div.slotmessage #price' + priceName).remove();
      $('.slotmessage').append('<div class="labelmessage" id="price' + priceName + '">' + message + '</div>');
    }, function(error) {
      // oops
      $('.loader-apicall').hide();
      $('body').removeClass('loader-body');
    });
  }

});
