for (let i = 0; i < pondFiles.length; i++) {
    const canvas = document.createElement('canvas');
    const ctx = canvas.getContext('2d');
    const img = new Image();
    img.src = URL.createObjectURL(pondFiles[i].file);
    img.onload = function() {
      canvas.width = 512;
      canvas.height = 512;
      ctx.drawImage(img, 0, 0, 512, 512);
      const dataURL = canvas.toDataURL('image/png');
      const blobBin = atob(dataURL.split(',')[1]);
      const array = [];
      for (let i = 0; i < blobBin.length; i++) {
        array.push(blobBin.charCodeAt(i));
      }
      const file = new File([new Uint8Array(array)], pondFiles[i].filename, {
        type: 'image/png'
      });
      console.log("file", file)
      pondFiles[i].file = file;
      convertedFiles.push(pondFiles[i].file);
    };
  }




  


  const canvas = document.createElement('canvas');
        const ctx = canvas.getContext('2d');
        // const resizedImages = {};
        // for (let i = 0; i < pondFiles.length; i++) {
        //   const img = new Image();
        //   img.src = URL.createObjectURL(pondFiles[i].file);
        //   img.onload = () => {
        //     canvas.width = 512;
        //     canvas.height = 512;
        //     ctx.drawImage(img, 0, 0, 512, 512);
        //     canvas.toBlob(blob => {
        //       const file = new File([blob], 
        //       //IF file name consists JPG or JPEG, change to PNG
        //       pondFiles[i].filename.includes("jpg") || pondFiles[i].filename.includes("jpeg") ? pondFiles[i].filename.replace(/jpg|jpeg/gi, "png") : pondFiles[i].filename, {
        //         type: 'image/png',
        //         lastModified: Date.now()
        //       });         
              
        //       //Add to object of resized images
        //       resizedImages[i] = file;  
              
        //       pondFiles[i].file = file;         
        //     }, 'image/png', 0.7);
        //   };
        // }
        // console.log("Resized first file:", resizedImages[0]) 


        //Add Resized Images to Form Data
        const formData = new FormData();
        for (let i = 0; i < pondFiles.length; i++) {
          const img = new Image();
          let createdFile = null;
          img.src = URL.createObjectURL(pondFiles[i].file);
          img.onload = () => {
            canvas.width = 512;
            canvas.height = 512;
            ctx.drawImage(img, 0, 0, 512, 512);
            canvas.toBlob(blob => {
              const file = new File([blob], 
              //IF file name consists JPG or JPEG, change to PNG
              pondFiles[i].filename.includes("jpg") || pondFiles[i].filename.includes("jpeg") ? pondFiles[i].filename.replace(/jpg|jpeg/gi, "png") : pondFiles[i].filename, {
                type: 'image/png',
                lastModified: Date.now()
              });         
              
              //Add to Created File
              createdFile = file;
              
              
              // pondFiles[i].file = file;         
            }, 'image/png', 0.7);
          };
          formData.append('files', createdFile);
          formData.append("test", "test")

          // formData.append('files', pondFiles[i].file);
        }
        // for(let i = 0; i < Object.keys(resizedImages).length; i++){
        //   formData.append('files', resizedImages[i]);
        // }
        
        console.log("Form Data:", formData.get('files'))















  //Resize images to make sure each image is 1MB or less with Canvas API
        const canvas = document.createElement('canvas');
        const ctx = canvas.getContext('2d');
        const MAX_WIDTH = 512;
        const MAX_HEIGHT = 512;
        const quality = 0.7;
        const resizedImages = [];
        for (let i = 0; i < pondFiles.length; i++) {
          const img = new Image();
          let returnFile = "";
          img.src = URL.createObjectURL(pondFiles[i].file);
          img.onload = function() {
            let width = img.width;
            let height = img.height;
            if (width > height) {
              if (width > MAX_WIDTH) {
                height *= MAX_WIDTH / width;
                width = MAX_WIDTH;
              }
            } else {
              if (height > MAX_HEIGHT) {
                width *= MAX_HEIGHT / height;
                height = MAX_HEIGHT;
              }
            }
            canvas.width = width;
            canvas.height = height;
            ctx.drawImage(img, 0, 0, width, height);
            const dataUrl = canvas.toDataURL('image/jpeg', quality);
            const blobBin = atob(dataUrl.split(',')[1]);
            const array = [];
            for (let i = 0; i < blobBin.length; i++) {
              array.push(blobBin.charCodeAt(i));
            }
            const file = new File([new Uint8Array(array)], 
            //Rename JPG and JPEG file to PNG
            pondFiles[i].filename.replace(/\.jpg$/i, '.png')
            , {
              type: 'image/png'              
            });
            returnFile = file;
           
          }
          console.log("Resized File:", returnFile)
            return resizedImages.push(returnFile);
            resizedImages.push("test")
        }