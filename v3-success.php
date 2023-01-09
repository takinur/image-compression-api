<?php
require 'vendor/autoload.php';
// $stripe = new \Stripe\StripeClient('sk_test_51M4SshJIS3ZRoorBZvHTYKdzJIklepCBntigzytKFnAClBnzBrM7cFHKHzvT9lBu5H08VLzpf0EQU0Int4KN1IgZ00BoDkWVV1');
$stripe = new \Stripe\StripeClient('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');
$session = $stripe->checkout->sessions->retrieve($_GET['session_id']);
$id = $session->id;
$customer_email = $session->customer_details->email;

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
  html, body {
      overflow-y: auto;
  }
  body {
      padding: 4vh 0;
  }
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
    .filepond--credits {
      display: none;
    }
    .filepond--file-wrapper{
      display: none;
    }
    /* Dont Scale Container of Filepond */
    .filepond--root {
      transform: none !important;
      height: 76px !important;
    }
    
  </style> 
  <title>Digital You</title>
</head>
<body>
<form id="form" enctype="multipart/form-data" name="imageUpload">
    <h2>Thanks for you purchase!</h2>

    
    <p>Our AI works better with high quality images. Please make sure the images you provide are clear and your face is easily identified. The better the images, the better results provided.</p>
    <br>
    <div style="position: relative; width:100% !important">
    <input id="files" style="width:100% !important" type="file" class="filepond" name="my_file[]" multiple>
    </div>

    <h5 id="fileCount" style="text-align: center; width:100%;" ></h5>
    <p style="text-align: center; font-size: 12px">Please upload <b>18-25</b> photos where your face is clearly visible (crop if necessary)</p>
    <label>Select your gender:</label>
    <select id="className">
        <option value="man">Man</option>
        <option value="woman">Woman</option>
    </select>
    <input type="hidden" id="userId" value="<?php echo $session ?>">
    <button style="cursor: pointer" type="submit" id="button">Upload</button>
    <br>
    <div style="display: flex; flex-direction: row; justify-content: center; align-items: center; gap: 10px">
      <div style="display: flex; flex-direction: column; justify-content: center; align-items: center">
        <img style="width: 100px; height: 100px; object-fit: cover" src="./assets/Chest_Up.jfif">
        <small style="font-size: 10px">10x close ups</small>
      </div>
      <div style="display: flex; flex-direction: column; justify-content: center; align-items: center">
        <img style="width: 100px; height: 100px; object-fit: cover" src="./assets/close-up.jfif">
        <small style="font-size: 10px">5x chest up</small>
      </div>
      <div style="display: flex; flex-direction: column; justify-content: center; align-items: center">
        <img style="width: 100px; height: 100px; object-fit: cover" src="./assets/side.jfif">
        <small style="font-size: 10px">3x side</small>
      </div>
    </div>
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
  <script src='https://code.jquery.com/jquery-3.6.0.min.js' integrity='sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=' crossorigin='anonymous'></script>
  <script>
    $(document).ready(function () {
        $("body").css("height", window.innerHeight);
        $(window)
          .resize(function () {
            $("body").css("height", window.innerHeight);
          })
          .resize();
    });
  </script>
  <script>

     //Initialize FilePond
    const input = document.querySelector('input[type="file"]');
    pond = FilePond.create(input,
    {
      instantUpload: false,
      allowProcess: false,
      storeAsFile: true,
      allowMultiple: true,
      maxFiles: 30,
      maxFileSize: '4MB',
      type: 'local',
      acceptedFileTypes: ['image/png', 'image/jpeg', 'image/jpg'],
      allowDrop: false, //Disable Drag and Drop
      labelIdle: '<span class="filepond--label-action">Browse</span> images',

    });

    const countLabel = document.getElementById('fileCount');

    //Count the files on FilePond
    pond.onaddfile = (fileItem) => {
      //Get the files from the pond
      let pondFiles = pond.getFiles();
      countLabel.innerHTML = "Total Selected Images: " + pondFiles.length;
    }

    //On Click event for FilePond browse button
    const browseButton = document.querySelector('.filepond--drop-label');
    browseButton.addEventListener('click', () => {
      pond.removeFiles();
    })

    //Form Submit Event
    const form = document.getElementById('form');
    form.addEventListener("submit", e => {
      e.preventDefault();

      const loader = document.getElementById('loader');
      const success = document.getElementById('success');
      const button = document.getElementById('button');

      //Get the files from the pond
      let pondFiles = pond.getFiles();
      
      //File less than 18 or more than 25 images
      if(pondFiles.length < 1){ //18
        alert("You need to load for more images for the magic to happen. Please make sure you have a minimum of 18 images of yourself.")
      } else if (pondFiles.length > 25){
        alert("We love your enthusiasm! The AI nanobots only need 20 pictures of you. Please make sure not to exceed this limit.");
      }
      else {
        button.disabled = true;
        form.style.display = 'none';
        loader.style.display = 'block';
      //Total size should be less thab 30MB
        let totalSize = 0;
        pondFiles.forEach(file => {
          totalSize += file.file.size;
        });
        if(totalSize > 1024 * 1024 * 150){
          alert("The total size of your images is too big. Please make sure the total size is less than 150MB.");
          return;
        }
        //Local State Variables
        const OrgFiles = pondFiles;
        let compFiles = [];

        //Compresion of images
        async function compressFile(orgFile){
          let file = orgFile.file;
          //Compression Config
          const options = {
            maxSizeMB: 1,
            maxWidthOrHeight: 512,
            useWebWorker: true,
            fileType: 'image/png',
          }
          try {
            const compressedFile = await imageCompression(file, options);
            // console.log(`compressedFile size ${compressedFile.size / 1024 / 1024} MB`); // smaller than maxSizeMB
            //Convert blop to file
            const newFile = new File([compressedFile], 
              file.name.includes("png") || file.name.includes("jpeg") ? file.name.replace(/png|jpeg/gi, "jpg") : file.name, 
              {type: compressedFile.type, lastModified: Date.now()}
            );          
            compFiles.push(newFile);          
          } catch (error) {
            console.log(error);
          }
        }
       
      async function sendFiles(){
        //Loop through the files and add compression
        for (let i = 0; i < OrgFiles.length; i++) {
          await compressFile(OrgFiles[i]);
        }
        const formData = new FormData();

        //Add the files to the form
        compFiles.map(file => {
          formData.append('files', file);
        });
        // console.log('compressed Files', compFiles)
        // console.log('These will be sent to the API', ...formData)
        // Send to API
      
        const select = document.getElementById('className');
        const className = select.options[select.selectedIndex].value;
      
        const urlParams = new URLSearchParams(window.location.search);
        const sessionId = urlParams.get('session_id');
  
        const API_BASE_URL = 'https://dreambooth-31489.web.app/api/'
  
        fetch(`${API_BASE_URL}upload?id=${sessionId}&class=${className}`, {
          method: 'POST',
          body: formData,
        }).then(() => {loader.style.display = 'none';success.style.display = 'flex';}).catch(console.error)
        <?php
        $header = "From: Digital You <bot@digitalyou.ai>";
        $header .= "MIME-Version: 1.0\r\n";
        $header .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
        $mensaje = '<!DOCTYPE html>
        <html lang="en">
        <head>
            <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
            <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
            <title>Digital You - Thanks</title>
            <link rel="preconnect" href="https://fonts.googleapis.com" />
            <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
            <link
              href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700&display=swap"
              rel="stylesheet"
            />
            <style>
                * {
                    font-family: "Poppins";
                    margin: 0;
                    padding: 0;
                }
                body {
                    height: 100vh;
                    width: 100%;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    background: linear-gradient(123.53deg, #0B3AB0 0%, #510A7A 100%);
                }
                section {
                    background: white;
                    border-radius: 10px;
                    width: 100%;
                    margin: 4vh 4vw;
                    max-width: 550px;
                    box-shadow: 0px 0px 20px rgb(0 0 0 / 25%);
                    padding: 4vh 6vw;
                    text-align: center;
                }
                p {
                    padding-bottom: 15px;
                    font-size: 16px;
                }
                @media only screen and (max-width: 600px) {
                    p {
                        font-size: 14px;
                    }
                }
            </style>
        </head>
        <body>
            <section>
                <h1>Hi there!</h1>
                <br>
                <p>We are happy to confirm that we have received your order for a Digital You AI avatar 😊 Your order is being worked on by our team of AI nanobots, and you can expect to receive your avatar via email in just a few hours.</p>
                <p>Thank you for choosing Digital You. We look forward to delivering your AI avatar and seeing what you do with it!</p>
                <p>Welcome to the AI world,</p>
                <p><b>Digital You Team</b></p>
            </section>
        </body>
        </html>';
        $mail_status = mail($customer_email, 'Digital You - Purchase', $mensaje, $header);
        // ?>   
             
       
      }
      //Call the function
      sendFiles();
      }
    })
  </script>

</body>
</html>