var $myCarousel = $("#myCarousel");
$myCarousel.on("slide.bs.carousel", function (event) {
	var $currentSlide = $myCarousel.find(".active iframe");
	// exit if there's no iframe, i.e. if this is just an image and not a video player
	if (!$currentSlide.length) { return; }
	// pass that iframe into Froogaloop, and call api("pause") on it.
	var player = Froogaloop($currentSlide[0]);
	player.api("pause");
});
