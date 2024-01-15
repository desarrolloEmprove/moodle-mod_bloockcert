function showSpinner() {
   // Hide the button.
   document.getElementById("download-button").style.display = "none";

   // Show spinner.
   document.getElementById("dynamic-text").style.display = "inline-block";
   document.getElementById("text-spinner").style.display = "inline-block";
   document.getElementById("loading-image").style.display = "inline-block";

   // List of texts to change.
   var texts = ["Generando certificado", "Firmando certificado", "Validando certificado", "Descargando certificado"];
   var textIndex = 0;
   var textElement = document.getElementById("dynamic-text");

   function changeText() {
      textElement.textContent = texts[textIndex];
      textIndex = (textIndex + 1) % texts.length;
      if (textIndex === 0) {
         textIndex = texts.length - 1;
      }
      setTimeout(changeText, 1000);
   }

   changeText();

   // Simulate a download.
   setTimeout(function () {
      // Hide spinner.
      document.getElementById("dynamic-text").style.display = "none";
      document.getElementById("text-spinner").style.display = "none";
      document.getElementById("loading-image").style.display = "none";

      // Shows the "Return to course" button.
      document.getElementById("title-succes").style.display = "inline-block";
      document.getElementById("return-button").style.display = "inline-block";
   }, 5000);
}
