//* Animated Text *//
const words = ["Learn", "Explore", "Discover"];
const text = document.querySelector(".text-animated");

const gen = (function* () {
  let index = 0;
  while (true) {
    yield index;
    index = (index + 1) % words.length;
  }
})();

const printChar = (word) => {
  let i = 0;
  text.textContent = "";
  const interval = setInterval(() => {
    if (i >= word.length) {
      clearInterval(interval);
      setTimeout(deleteChar, 1500); 
    } else {
      text.textContent += word[i++];
    }
  }, 50);
};

const deleteChar = () => {
  let i = text.textContent.length;
  const interval = setInterval(() => {
    if (i > 0) {
      text.textContent = text.textContent.slice(0, --i);
    } else {
      clearInterval(interval);
      setTimeout(() => {
        printChar(words[gen.next().value]); // Add delay before next word
      }, 200);
    }
  }, 50);
};

printChar(words[gen.next().value]);

//* End of Animated Text *//
