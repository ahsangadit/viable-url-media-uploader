(function () {
	'use strict';

	var upsellBtn = document.querySelector('.vumu-pro-upsell-add-btn');

	if (!upsellBtn || !window.vumuProUpsell) {
		return;
	}

	upsellBtn.addEventListener('click', function (event) {
		event.preventDefault();
		event.stopPropagation();

		if (window.vumuProUpsell.upgradeUrl) {
			window.open(window.vumuProUpsell.upgradeUrl, '_blank', 'noopener,noreferrer');
		}
	});
})();
