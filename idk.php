<?php
// require 'vendor/autoload.php';
// // $stripe = new \Stripe\StripeClient('');
// $stripe = new \Stripe\StripeClient('');
// $session = $stripe->checkout->sessions->retrieve($_GET['session_id']);
// $id = $session->id;
// $customer_email = $session->customer_details->email;

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="./style/form.css?ver=1.0.1">
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://unpkg.com/filepond@^4/dist/filepond.css" rel="stylesheet" />
  <link
    href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700&display=swap"
    rel="stylesheet"
  />
  <style>
    small {
      width: 70%;
      margin: auto;
      font-size: 14px !important;
    }
    @media only screen and (max-width: 640px) {
      small {
        width: 80%;
        font-size: 10px !important;
      }
    }
    
  </style> 
  <title>Digital You</title>
</head>
<body>
<form id="form" enctype="multipart/form-data">
    <h2>Thanks for you purchase!</h2>

    
    <p>Our AI works better with high quality images. Please make sure the images you provide are clear and your face is easily identified. The better the images, the better results provided.</p>
    <br>
    <input id="files" type="file" class="filepond" name="my_file[]" multiple>
    <p style="text-align: center"><b>18-25</b></p>
    <label>Select your gender:</label>
    <select id="className">
        <option value="man">Man</option>
        <option value="woman">Woman</option>
    </select>
    <input type="hidden" id="userId" value="<?php echo $session ?>">
    <button style="cursor: pointer" type="submit" id="button">Upload</button>
    <br>
    <small>•If possible, please submit photos with different lightning, facial expressions and locations<br>
      •Show your shoulders<br>
      •Avoid having others in your photos<br>
      •No sunglasses, nudes or inappropriate content</small>
  </form>
  <div class="loader" id="loader"></div>
  <div id="success">
    <h4>Everything looks great! The AI nanobots have started crafting your Digital You. You will receive an email with your content in a few hours 😊</h4>
    <br>
    <a href="./index.html">Return to main page</a>
  </div>
  <script src="https://unpkg.com/filepond@^4/dist/filepond.js"></script>
  <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/browser-image-compression@2.0.0/dist/browser-image-compression.js"></script>


  <script>

     //Initialize FilePond
    const input = document.querySelector('input[type="file"]');
    pond = FilePond.create(input,
    {
      instantUpload: false,
      // allowProcess: false,
      storeAsFile: true,
      allowMultiple: true,
      maxFiles: 30,
      maxFileSize: '4MB',
      type: 'local',
      acceptedFileTypes: ['image/png', 'image/jpeg', 'image/jpg'],
    });

    // let compFiles = [];
    // //OnFilepond add file event listener 
    // pond.onaddfile = (error, file) => {
    //   if (error) {
    //     return;
    //   }      
    //   //Add the file to the array
    //   compFiles.push(file);

    //   compressImage(file);

    //   console.log('compFiles', compFiles)
    // };
    // //OnFilepond remove file event listener
    // pond.onremovefile = (error, file) => {
    //   if (error) {
    //     return;
    //   }
    //   //Pop the file from the array
    //   compFiles.pop(file);
    //   console.log('Removed', compFiles)
    // };


    // function compressImage (fileToComp){
    //     const file = fileToComp.file
    //     // Compress Files to 512x512 
    //     const MAX_WIDTH = 512; 
    //     const MAX_HEIGHT = 512;
    //     const MIME_TYPE = "image/png";
    //     const QUALITY = 0.7; //70% quality
        
    //     const canvas = document.createElement('canvas');
    //     const ctx = canvas.getContext('2d');
    //     const img = new Image();
    //     const blobURL = URL.createObjectURL(file);
    //     img.src = blobURL
    //     img.onload = function () {
    //       URL.revokeObjectURL(this.src);
    //       const [newWidth, newHeight] = calculateSize(img, MAX_WIDTH, MAX_HEIGHT);
    //       const canvas = document.createElement("canvas");
    //       canvas.width = newWidth;
    //       canvas.height = newHeight;
    //       const ctx = canvas.getContext("2d");
    //       ctx.drawImage(img, 0, 0, newWidth, newHeight);
        
    //     canvas.toBlob(blob => {
    //         const newFile = new File([blob], 
    //           file.name, 
    //           {type: MIME_TYPE, lastModified: Date.now()}
    //         ); 
    //         storeCompImage(newFile);
            
            
    //       }, MIME_TYPE, QUALITY);                
    //         // Create a new image and show it
    //         document.body.append(canvas);
    //     }
        
    // }

    // let compressedFiles = [];

    // function storeCompImage (file){
    //     console.log('hehe', file)
    //     compressedFiles.push(file);
    // }
    // console.log('compressedFiles', compressedFiles)

    //Calcuate the size of the image
    function calculateSize(img, maxWidth, maxHeight) {
          let width = img.width;
          let height = img.height;

          // calculate the width and height, constraining the proportions
          if (width > height) {
            if (width > maxWidth) {
              height = Math.round((height * maxWidth) / width);
              width = maxWidth;
            }
          } else {
            if (height > maxHeight) {
              width = Math.round((width * maxHeight) / height);
              height = maxHeight;
            }
          }
          return [width, height];
    }
        
  
      form.addEventListener("submit", e => {
      e.preventDefault();

      const loader = document.getElementById('loader');
      const success = document.getElementById('success');
      const button = document.getElementById('button');

      //Get the files from the pond
      let pondFiles = pond.getFiles();

      
      console.log("Uploaded Files:", pondFiles)     
      
      //File less than 18 or more than 25 images
      if(pondFiles.length < 1){ //18
        alert("You need to load for more images for the magic to happen. Please make sure you have a minimum of 18 images of yourself.")
      } else if (pondFiles.length > 25){
        alert("We love your enthusiasm! The AI nanobots only need 20 pictures of you. Please make sure not to exceed this limit.");
      }
      else {
        const formData = new FormData();
        
        console.log("Preparing Validations")
        // let files = pondFiles.map(file => file.file);

        const OrgFiles = pondFiles;
        let allFiles = [];

        //Check each file size and type
        const MB_IN_BYTES = 1024 * 1024;
        let fileLength = 0;

        
        async function compressFile(orgFiles){


          let file = orgFiles[0].file;

          const options = {
            maxSizeMB: 1,
            maxWidthOrHeight: 512,
            useWebWorker: true,
            fileType: 'image/png',
          }
          try {
            const compressedFile = await imageCompression(file, options);
            console.log('compressedFile instanceof Blob', compressedFile instanceof Blob); // true
            console.log(`compressedFile size ${compressedFile.size / 1024 / 1024} MB`); // smaller than maxSizeMB
            //Convert blop to file
            const newFile = new File([compressedFile], 
              file.name, 
              {type: compressedFile.type, lastModified: Date.now()}
            );          
            allFiles.push(newFile);
            console.log('copressed', compressedFile)
          

          } catch (error) {
            console.log(error);
          }
        }

        compressFile(OrgFiles);
       
      //When all files are compressed and ready to be sent to the server
      function sendFiles(){
        console.log('allFiles', allFiles)
        //Send the files to the server
        const formData = new FormData();
        allFiles.forEach(file => {
          formData.append('files', file);
        });
        console.log('formData', ...formData)

      }
      
      sendFiles();




        // //Main function to compress files
        // function compressFile (OrgFiles) {
        //   return new Promise((resolve, reject) => {          
        //   for (let i = 0; i < OrgFiles.length; i++) {

        //     const file = OrgFiles[i].file;

        //     // console.log("File Size:", file.size / MB_IN_BYTES, "MB")
        //     fileLength += file.size;
        //     //IF file type is not jpg or png
        //     if (file.type != "image/jpeg" && file.type != "image/png"){
        //       alert("One of your images is not a valid image type. Please make sure all images are in JPG or PNG format.")
        //       return;
        //     }

        //     console.log('Current Form data', ...formData)


        //     // Compress Files to 512x512 
        //     const MAX_WIDTH = 512; 
        //     const MAX_HEIGHT = 512;
        //     const MIME_TYPE = "image/png";
        //     const QUALITY = 0.7; //70% quality
            
        //     const canvas = document.createElement('canvas');
        //     const ctx = canvas.getContext('2d');
        //     const img = new Image();
        //     const blobURL = URL.createObjectURL(file);
        //     img.src = blobURL;

        //     img.onload = function () {
        //       URL.revokeObjectURL(this.src);
        //       const [newWidth, newHeight] = calculateSize(img, MAX_WIDTH, MAX_HEIGHT);
        //       const canvas = document.createElement("canvas");
        //       canvas.width = newWidth;
        //       canvas.height = newHeight;
        //       const ctx = canvas.getContext("2d");
        //       ctx.drawImage(img, 0, 0, newWidth, newHeight);
            
        //     canvas.toBlob(blob => {
        //         const newFile = new File([blob], 
        //           file.name, 
        //           {type: MIME_TYPE, lastModified: Date.now()}
        //         ); 
        //         return saveCompressedFile(newFile);
                
                
        //       }, MIME_TYPE, QUALITY);

            
                
        //       // Create a new image and show it
        //       document.body.append(canvas);


        //     };
          
        //   }
        //    resolve (allFiles);
        //   });
          
        // }

        // function saveCompressedFile(file) {
        //     console.log('Display Info', file)

        //     // if (file.name.includes("jpg") || file.name.includes("jpeg")){
        //     //   file.name = file.name.replace(/jpg|jpeg/gi, "png");
        //     // }
        //     // console.log('All Files', allFiles)

        //     return allFiles.push(file);
        //   }

        // //IF total size is more than 30M
        // if (fileLength > 30 * MB_IN_BYTES){
        //   alert("Your images are too big. Please make sure the total size of your images is less than 30MB.")
        //   return;
        // }
       

     

        // //Compress files
        // async function sendFiles() {
        //   const result = await compressFile(OrgFiles);
        //   console.log('Result', result)
        //   console.log('This is it Files', allFiles)
        //   allFiles.forEach(file => {
        //     formData.append('files', file);
        //   });

        //   console.log('Final Form', ...formData)

        // }

        // sendFiles();


        console.log('Before form, All Files', allFiles)
      
        //If all files are same length of filepond files
        // if (allFiles.length == pondFiles.length){
        //   console.log('All Files', allFiles)
        //   allFiles.forEach(file => {
        //     formData.append('files', file);
        //   });
        // } else {
        //   alert("Something went wrong. Please try again.")
        //   return;
        // }
     
        //Send to API
        console.log("Form Sukkess", ...formData)

        // button.disabled = true;
        // form.style.display = 'none';
        // loader.style.display = 'block';
      
        // const select = document.getElementById('className');
        // const className = select.options[select.selectedIndex].value;
      
        // const urlParams = new URLSearchParams(window.location.search);
        // const sessionId = urlParams.get('session_id');
  
        // const API_BASE_URL = 'YOUR_API_BASE_URL'
  
        // fetch(`${API_BASE_URL}upload?id=${sessionId}&class=${className}`, {
        //   method: 'POST',
        //   body: formData,
        // }).then(() => {loader.style.display = 'none';success.style.display = 'flex';}).catch(console.error)
        // <?php
        // $header = "From: Digital You <noreply@example.com>";
        // $header .= "MIME-Version: 1.0\r\n";
        // $header .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
        // $mensaje = '<!DOCTYPE html>
        // <html lang="en">
        // <head>
        //     <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
        //     <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        //     <title>Digital You - Thanks</title>
        //     <link rel="preconnect" href="https://fonts.googleapis.com" />
        //     <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
        //     <link
        //       href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700&display=swap"
        //       rel="stylesheet"
        //     />
        //     <style>
        //         * {
        //             font-family: "Poppins";
        //             margin: 0;
        //             padding: 0;
        //         }
        //         body {
        //             height: 100vh;
        //             width: 100%;
        //             display: flex;
        //             justify-content: center;
        //             align-items: center;
        //             background: linear-gradient(123.53deg, #0B3AB0 0%, #510A7A 100%);
        //         }
        //         section {
        //             background: white;
        //             border-radius: 10px;
        //             width: 100%;
        //             margin: 4vh 4vw;
        //             max-width: 550px;
        //             box-shadow: 0px 0px 20px rgb(0 0 0 / 25%);
        //             padding: 4vh 6vw;
        //             text-align: center;
        //         }
        //         p {
        //             padding-bottom: 15px;
        //             font-size: 16px;
        //         }
        //         @media only screen and (max-width: 600px) {
        //             p {
        //                 font-size: 14px;
        //             }
        //         }
        //     </style>
        // </head>
        // <body>
        //     <section>
        //         <h1>Hi there!</h1>
        //         <br>
        //         <p>We are happy to confirm that we have received your order for a Digital You AI avatar 😊 Your order is being worked on by our team of AI nanobots, and you can expect to receive your avatar via email in just a few hours.</p>
        //         <p>Thank you for choosing Digital You. We look forward to delivering your AI avatar and seeing what you do with it!</p>
        //         <p>Welcome to the AI world,</p>
        //         <p><b>Digital You Team</b></p>
        //     </section>
        // </body>
        // </html>';
        // $mail_status = mail($customer_email, 'Digital You - Purchase', $mensaje, $header);
        // ?>   
             
       
      }
    })
  </script>

</body>
</html>