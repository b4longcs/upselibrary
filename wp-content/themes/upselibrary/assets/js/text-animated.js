  
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
      deleteChar();
    } else {
      text.textContent += word[i++];
    }
  }, 200);
};

const deleteChar = () => {
  let i = text.textContent.length;
  const interval = setInterval(() => {
    if (i > 0) {
      text.textContent = text.textContent.slice(0, --i);
    } else {
      clearInterval(interval);
      printChar(words[gen.next().value]);
    }
  }, 100);
};

printChar(words[gen.next().value]);

//* End of Animated Text *//
