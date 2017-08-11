function toggleInstructions(e) {
    jQuery (e.target)
         .prev('.panel-heading')
         .find("i.indicator")
         .toggleClass('glyphicon-triangle-bottom glyphicon-triangle-right');
}
jQuery ('#accordion').on('hidden.bs.collapse', toggleInstructions);
jQuery ('#accordion').on('shown.bs.collapse', toggleInstructions);