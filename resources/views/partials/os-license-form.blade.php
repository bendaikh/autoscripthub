{{--
  Single price + optional Games when the chosen category is Operating System
  Licenses or one of its subcategories. Other categories, including scripts
  and other Serials, keep the current Regular / Extended fields.
--}}
<script type="text/javascript">
$(document).ready(function () {
	'use strict';
	var osSelections = @json(\Fickrr\Support\OsLicense::selectionValues());
	var priceLabel = @json(__('Price'));
	var itemType = $('input[name="item_type"]').val() || '';
	var osApplied = false;
	var $regular = $('#regular_price');
	var $extended = $('#extended_price');
	var $left = $('#pricebox_left');
	var $right = $('#pricebox_right');
	var $leftLabel = $left.find('label').first();
	var savedRegular = $regular.val();
	var savedExtended = $extended.val();
	var savedRegularRule = $regular.attr('data-bvalidator') || '';
	var savedExtendedRule = $extended.attr('data-bvalidator') || 'required,min[1]';
	if ($leftLabel.length && !$leftLabel.data('osOriginal')) {
		$leftLabel.data('osOriginal', $leftLabel.html());
	}

	function isOsCategory() {
		var selected = $('#item_category').val() || '';
		return osSelections.indexOf(selected) !== -1;
	}

	function isScriptType() {
		return itemType === 'scripts' || itemType === 'script';
	}

	function setOptionalAttributes(optional) {
		$('.js-os-optional-attribute').each(function () {
			var $group = $(this);
			$group.find('input, select, textarea').each(function () {
				var $field = $(this);
				if (!$field.data('osSavedRule')) {
					$field.data('osSavedRule', $field.attr('data-bvalidator') || '');
				}
				if (optional) {
					$field.removeAttr('data-bvalidator');
				} else if ($field.data('osSavedRule')) {
					$field.attr('data-bvalidator', $field.data('osSavedRule'));
				}
			});
			$group.find('.require').toggle(!optional);
		});
	}

	function showSinglePrice() {
		$('#pricebox').show().removeClass('display-none force-none');
		$left.show().removeClass('display-none force-none').addClass('display-block');
		$right.hide().removeClass('display-block force-block');
		$leftLabel.html(priceLabel + ' <span class="require">*</span>');
		$regular.prop('disabled', false).attr('data-bvalidator', 'required,min[1]');
		if ($regular.val() === '0' && savedRegular && savedRegular !== '0') {
			$regular.val(savedRegular);
		}
		if ($extended.length) {
			$extended.val('0').removeAttr('data-bvalidator').prop('disabled', true);
		}
		setOptionalAttributes(true);
		osApplied = true;
	}

	function showScriptPrice() {
		$left.hide();
		$right.show().removeClass('display-none force-none col-sm-6').addClass('display-block col-sm-12');
		if ($leftLabel.data('osOriginal')) {
			$leftLabel.html($leftLabel.data('osOriginal'));
		}
		$regular.val('0').removeAttr('data-bvalidator').prop('disabled', false);
		if ($extended.length) {
			$extended.prop('disabled', false).attr('data-bvalidator', 'required,min[1]');
			if ((!$extended.val() || $extended.val() === '0') && savedExtended && savedExtended !== '0') {
				$extended.val(savedExtended);
			}
		}
		setOptionalAttributes(false);
		osApplied = false;
	}

	function showBothPrices() {
		var support = $('#item_support').length ? String($('#item_support').val() || '') : '1';
		if ($leftLabel.data('osOriginal')) {
			$leftLabel.html($leftLabel.data('osOriginal'));
		}
		$regular.prop('disabled', false);
		if (savedRegularRule) {
			$regular.attr('data-bvalidator', savedRegularRule);
		} else {
			$regular.removeAttr('data-bvalidator');
		}
		if (savedRegular !== undefined && $regular.val() === '0' && savedRegular !== '0') {
			$regular.val(savedRegular);
		}
		if ($extended.length) {
			$extended.prop('disabled', false).attr('data-bvalidator', savedExtendedRule || 'required,min[1]');
			if ((!$extended.val() || $extended.val() === '0') && savedExtended && savedExtended !== '0') {
				$extended.val(savedExtended);
			}
		}
		if (support === '0') {
			$('#pricebox, #pricebox_left, #pricebox_right').hide();
		} else {
			$('#pricebox').show().removeClass('display-none force-none');
			$left.show().removeClass('display-none force-none col-sm-12').addClass('display-block');
			$right.show().removeClass('display-none force-none col-sm-12').addClass('display-block');
		}
		setOptionalAttributes(false);
		osApplied = false;
	}

	function applyOsLicensePrice() {
		if (isOsCategory()) {
			showSinglePrice();
			return;
		}
		if (!osApplied && !isScriptType()) {
			setOptionalAttributes(false);
			return;
		}
		if (isScriptType()) {
			showScriptPrice();
			return;
		}
		showBothPrices();
	}

	$('#item_category').on('change', function () {
		setTimeout(applyOsLicensePrice, 0);
	});
	$('#item_support').on('change', function () {
		if (osApplied || isOsCategory()) {
			setTimeout(applyOsLicensePrice, 0);
		}
	});
	setTimeout(applyOsLicensePrice, 0);
});
</script>
