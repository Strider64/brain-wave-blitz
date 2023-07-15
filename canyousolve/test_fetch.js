
// An array to hold your Image objects
let imageArray = [];

// A function to preload the images
const preloadImages = imagePaths => {
    for (let i = 0; i < imagePaths.length; i++) {
        let img = new Image();
        img.src = imagePaths[i];
        imageArray.push(img);
    }

    console.log(imageArray);
};

fetch('fetch_game_image_paths.php')
    .then(response => response.json())
    .then(data => preloadImages(data))
    .catch(error => console.error('Error:', error));
