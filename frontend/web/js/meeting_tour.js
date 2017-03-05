var tour;

tour = new Shepherd.Tour({
  defaults: {
    classes: 'shepherd-theme-arrows',
    scrollTo: true
  }
});

tour.addStep('example-step', {
  text: 'This step is attached to the bottom of the <code>.example-css-selector</code> element.',
  attachTo: '.example-css-selector bottom',
  classes: 'example-step-extra-class',
  buttons: [
    {
      text: 'Next',
      action: tour.next
    }
  ]
});

tour.start();
