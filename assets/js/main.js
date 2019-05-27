$(document).ready(function () {
	$(document).on("scroll", onScroll);

	$(function () {
		$('#mix-wrapper').mixItUp({
			load: {
				sort: 'order:asc'
			},
			animation: {
				effects: 'fade rotateZ(-180deg)',
				duration: 700
			},
			selectors: {
				target: '.mix-target',
				filter: '.filter-btn',
				sort: '.sort-btn'
			}
			// callbacks: {
			// 	onMixEnd: function (state) {
			// 		console.log(state)
			// 	}
			// }
		});
	});
});

function onScroll(event) {
	var scrollPosition = $(document).scrollTop();
	$('.nav li a').each(function () {
		var currentLink = $(this);
		var refElement = $(currentLink.attr("href"));
		if (refElement.position().top <= scrollPosition && refElement.position().top + refElement.height() > scrollPosition) {
			$('ul.nav li a').removeClass("navactive");
			currentLink.addClass("navactive");
		}
		else {
			currentLink.removeClass("navactive");
		}
	});

};
