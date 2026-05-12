function convertToMinutes(time) {
  const parts = time.split(':');

  return parseInt(parts[0], 10) * 60 + parseInt(parts[1], 10);
}

function banglaNumber(value) {
  const banglaDigits = ['০', '১', '২', '৩', '৪', '৫', '৬', '৭', '৮', '৯'];

  return value.toString().replace(/\d/g, function (digit) {
    return banglaDigits[digit];
  });
}

function initPrayerCountdown() {
  const nameElement = document.getElementById('upcoming-prayer-name');
  const countElement = document.getElementById('upcoming-prayer-countdown');

  if (!nameElement || !countElement) {
    return;
  }

  const times = [
    { name: 'ফজর', time: '04:20' },
    { name: 'যোহর', time: '12:05' },
    { name: 'আসর', time: '16:30' },
    { name: 'মাগরিব', time: '18:30' },
    { name: 'এশা', time: '19:45' },
  ];

  function updateCountdown() {
    const now = new Date();
    const currentMinutes = now.getHours() * 60 + now.getMinutes();
    let runningPrayer = null;
    let difference = 0;

    for (let index = 0; index < times.length; index += 1) {
      const prayerMinutes = convertToMinutes(times[index].time);

      if (currentMinutes >= prayerMinutes) {
        const nextIndex = index + 1;

        if (nextIndex < times.length) {
          const nextPrayerMinutes = convertToMinutes(times[nextIndex].time);

          if (currentMinutes < nextPrayerMinutes) {
            runningPrayer = times[index];
            difference = nextPrayerMinutes - currentMinutes;
            break;
          }
        } else {
          runningPrayer = times[index];
          difference = (24 * 60 - currentMinutes) + convertToMinutes(times[0].time);
          break;
        }
      }
    }

    if (!runningPrayer) {
      runningPrayer = times[times.length - 1];
      difference = convertToMinutes(times[0].time) - currentMinutes;
      if (difference < 0) difference += 24 * 60;
    }

    const hours = Math.floor(difference / 60);
    const minutes = difference % 60;

    nameElement.innerText = runningPrayer.name;
    countElement.innerText = banglaNumber(hours.toString().padStart(2, '0'))
      + ' ঘণ্টা '
      + banglaNumber(minutes.toString().padStart(2, '0'))
      + ' মিনিট';
  }

  updateCountdown();
  window.setInterval(updateCountdown, 60000);
}

document.addEventListener('DOMContentLoaded', initPrayerCountdown);
