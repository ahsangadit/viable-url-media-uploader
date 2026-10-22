(function () {
	'use strict';

	function openUpgrade(url) {
		var target = url || (window.vumuProUpsell && window.vumuProUpsell.upgradeUrl);
		if (target) {
			window.open(target, '_blank', 'noopener,noreferrer');
		}
	}

	var upsellBtn = document.querySelector('.vumu-pro-upsell-add-btn');
	if (upsellBtn) {
		upsellBtn.addEventListener('click', function (event) {
			event.preventDefault();
			event.stopPropagation();
			openUpgrade();
		});
	}

	document.querySelectorAll('.vumu-pro-upsell-import-btn').forEach(function (btn) {
		btn.addEventListener('click', function (event) {
			event.preventDefault();
			openUpgrade(btn.getAttribute('data-vumu-upgrade-url'));
		});
	});
})();
