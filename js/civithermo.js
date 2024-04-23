function civithermo_render() {
  // Load data from Civi data object
  const goal = parseInt(CRM.vars.civithermo.amountGoal);
  const stretch = parseInt(CRM.vars.civithermo.amountStretch);
  const raised = parseInt(CRM.vars.civithermo.amountRaised);
  const currency = CRM.vars.civithermo.currency;
  const donors = CRM.vars.civithermo.numberDonors;
  const isDouble = parseInt(CRM.vars.civithermo.isDouble);

  // Declare thermometer elements
  let thermo_target = document.getElementsByClassName('civithermo_target')[0];
  let thermo_total = document.getElementsByClassName('civithermo_total')[0];
  let thermo_amount = document.getElementsByClassName('civithermo_amount')[0];
  let thermo_donors = document.getElementsByClassName('civithermo_donors')[0];
  let thermo_raised = document.getElementsByClassName('civithermo_raised')[0];
  let thermo_percent = Math.floor((raised / goal) * 100);

  // Get browser locale
  const locale = navigator.language;

  // If there's no goal amount then we exit early
  if ( isNaN(goal) ) { return };

  // Manipulate thermometer elements

  if (!isNaN(stretch) && raised >= goal) {
    thermo_target.innerHTML = 'TARGET <span style="text-decoration: line-through">'
      + goal.toLocaleString(locale, {style: 'currency', currency: currency, minimumFractionDigits: 0})
      + '</span> '
      + stretch.toLocaleString(locale, {style: 'currency', currency: currency, minimumFractionDigits: 0});
    thermo_percent = Math.floor((raised / stretch) * 100);
  } else {
    thermo_target.innerHTML = 'TARGET ' + goal.toLocaleString(locale, {style: 'currency', currency: currency, minimumFractionDigits: 0});
  }

  if (isDouble) {
    thermo_total.innerHTML = raised.toLocaleString(locale, {style: 'currency', currency: currency, minimumFractionDigits: 0})
      + ' DONATED MEANS <br />'
      + (2 * raised).toLocaleString(locale, {style: 'currency', currency: currency, minimumFractionDigits: 0}) + ' SO FAR';
  } else {
    thermo_total.innerHTML = raised.toLocaleString(locale, {style: 'currency', currency: currency, minimumFractionDigits: 0}) + ' SO FAR';
  }

  thermo_amount.style.height = thermo_percent + '%';
  thermo_donors.innerHTML = donors + ' donors';
  thermo_raised.innerHTML = thermo_percent + '% raised';
}
