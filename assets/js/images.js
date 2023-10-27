'use strict';
(function () {
    let category = document.querySelector('#category');
    let container = document.querySelector('.container');
    let sidebar = document.querySelector('.sidebar_pages');
    let lightbox = document.querySelector('.lightbox');

    let current_page = 1, per_page = 4, offset = 0;
    let database_data = {
        'category': 'general',
        'current_page': current_page,
        'per_page': per_page,
        'total_count': 0,
        'offset': offset
    };
    let pages = [{}];
    let total_pages = 0;

    /* Handle General Errors in Fetch */
    const handleErrors = function (response) {
        if (!response.ok) {
            throw (response.status + ' : ' + response.statusText);
        }
        return response;
    };


    /*
     * FETCH for New Category
     */
    const categoryUISuccess = async (parsedData) => {
        /* Remove Image For Screen (cleanup) */
        //console.log('parsedData', parsedData, 'database_data', database_data);
        while (container.firstChild) {
            container.removeChild(container.firstChild)
        }

        let count = 0; // For different class names for size boxes in CSS

        await parsedData.forEach(slide => {
            /* Main Image Slide Block */
            let displayDiv = document.createElement('div');
            /* Array of different size class names for CSS */
            let displayFormat = ["gallery-container w-3 h-2", 'gallery-container w-3 h-2',
                'gallery-container w-3 h-2', 'gallery-container w-3 h-2',
                'gallery-container w-3 h-2', 'gallery-container w-3 h-2'];
            displayDiv.className = `${displayFormat[count]}`; //Assign Class Names to Div:
            container.appendChild(displayDiv); //Append Child to Parent Div:

            /*
             * Create div for indiviual images
             */
            let galleryItem = document.createElement('div');
            galleryItem.classList.add('gallery-item');
            displayDiv.appendChild(galleryItem);
            /*
             * Image div element
             */
            let images = document.createElement('div');
            images.classList.add('images');
            galleryItem.appendChild(images);
            /*
             * Image itself
             */
            let galleryImage = document.createElement('img')
            galleryImage.src = slide.image_path;
            galleryImage.setAttribute('alt', slide.content); // Description of what image is about:
            galleryImage.setAttribute('loading', 'lazy'); // Add lazy loading attribute
            /* Set EXIF info to data-exif attribute */
            galleryImage.setAttribute('data-exif', slide.Model + ' ' + slide.ExposureTime + ' ' + slide.Aperture + ' '
                + slide.ISO + ' ' + slide.FocalLength);
            images.appendChild(galleryImage); // Append image to Image div element:
            /*
             * Hidden Paragraph
             */
            let paragraph = document.createElement('p');
            paragraph.classList.add('hideContent');
            paragraph.textContent = slide.content;
            images.appendChild(paragraph);
            /*
             * Title Block
             */
            let title = document.createElement('div');
            title.classList.add('title');
            galleryItem.appendChild(title);
            /*
             * Heading 1
             */
            let heading1 = document.createElement('h1');
            heading1.classList.add('pictureHeading');
            heading1.textContent = `${slide.heading[0].toUpperCase()}${slide.heading.slice(1)}`;
            title.appendChild(heading1);
            let titleSpan = document.createElement('span');
            titleSpan.classList.add('exifInfo');
            titleSpan.textContent = slide.Model;
            title.appendChild(titleSpan);

            count += 1;
        })

        const images = document.querySelectorAll('img')

        images.forEach(image => {

            /* Add Event Listener to Images and setting css class to active */
            image.addEventListener('click', () => {
                //document.getElementById('gallery_category').style.display = 'none';
                //document.querySelector('.sidebar_pages').style.display = 'none';
                lightbox.classList.add('active');
                document.querySelector('.container').style.display = 'none';

                // Create the common container for galleryImage and galleryExif
                let imageExifContainer = document.createElement('div');
                imageExifContainer.classList.add('image-exif-container');
                /*
                 * Create Image portion of LightBox
                 */
                let galleryImage = document.createElement('img');
                galleryImage.classList.add('galleryImage');
                galleryImage.width = 800;
                galleryImage.height = 534;
                galleryImage.src = image.src // image path

                /*
                 * Create EXIF portion of LightBox
                 */
                let galleryExif = document.createElement('p');

                if  (image.getAttribute('data-exif') === 'null null null null null') {
                    galleryExif.classList.add('galleryExif');
                    galleryExif.textContent = 'No EXIF data available';
                } else {
                    galleryExif.classList.add('galleryExif');
                    galleryExif.textContent = image.getAttribute('data-exif');
                }

                // Add both elements to the common container
                imageExifContainer.appendChild(galleryImage);
                imageExifContainer.appendChild(galleryExif);

                /*
                 * Create Text portion of Lightbox
                 */

                let nextSibling = image.nextElementSibling; // Grab the next sibling:
                let galleryText = document.createElement('p');
                galleryText.classList.add('galleryText');

                galleryText.textContent = nextSibling.textContent;
                //console.log('galleryText', galleryText);
                /* Remove large Image For Screen (cleanup) */
                while (lightbox.firstChild) {
                    lightbox.removeChild(lightbox.firstChild)
                }

                // Add the container to the lightbox
                lightbox.appendChild(imageExifContainer);

                /* Add Content to Screen */
                lightbox.appendChild(galleryText);
            })
        })
        lightbox.addEventListener('click', () => {
            if (lightbox.hasChildNodes()) {
                document.getElementById('gallery_category').style.display = 'block';
                document.querySelector('.sidebar_pages').style.display = 'flex';
                lightbox.classList.remove('active'); // Exit Lightbox by removing active css class
                lightbox.classList.add('lightbox');
                document.querySelector('.container').style.display = 'grid';
                //document.querySelector('.pagination').style.display = 'flex';
            }
        })
    };

    const categoryUIError = (error) => {
        console.log("Database Table did not load", error);
    }
    /* create FETCH request */
    const createImageRequest = async (url, succeed, fail) => {
        //console.log('database_data', database_data);

        try {
            const response = await fetch(url, {
                method: 'POST', // or 'PUT'
                body: JSON.stringify(database_data),
            });

            handleErrors(response);

            const data = await response.json();
            succeed(data);
        } catch (error) {
            fail(error);
        }
    };

    const restLinks = () => {
        /* Remove Links For Screen (cleanup) */

        while (sidebar.firstChild) {
            sidebar.removeChild(sidebar.firstChild)
        }
    }
    const paginationUISuccess = async (parsedData) => {

        restLinks();

        console.log('total_count - Parsed Data', database_data.total_count);
        database_data.offset = await parsedData.offset;
        total_pages = Math.ceil(database_data.total_count / database_data.per_page);

        /* Create the Display Links and add an event listener */
        pages = [{}];
        /*
         * Creating the array of page object(s)
         */
        for (let x = 0; x < total_pages; x++) {
            pages[x] = {page: x + 1};
        }

        pages.forEach(link_page => {
            //console.log('link_page', link_page);
            const links = document.createElement('div');
            links.className = 'links';
            sidebar.appendChild(links);
            /*
             * Add event listener for the links
             */
            links.addEventListener('click', () => {
                database_data.current_page = link_page.page;

                // Close the lightbox if it's currently active
                if (lightbox.classList.contains('active')) {
                    lightbox.classList.remove('active');
                    document.getElementById('gallery_category').style.display = 'block';
                    document.querySelector('.sidebar_pages').style.display = 'flex';
                    document.querySelector('.container').style.display = 'grid';
                }

                createRequest('galleryPagination.php', paginationUISuccess, paginationUIError);
            });

            const pageText = document.createElement('p');
            pageText.className = 'linkStyle';
            pageText.id = 'page_' + link_page.page;
            pageText.textContent = link_page.page;
            links.appendChild(pageText);
            if (database_data.current_page === link_page.page) {
                links.style.backgroundColor = "#00b28d";
            }
        })

        await createImageRequest('galleryImagesGet.php', categoryUISuccess, categoryUIError);

    };

    const paginationUIError = (error) => {
        console.log("Database Table did not load", error);
    };

    /* create FETCH request */
    const createRequest = async (url, succeed, fail) => {
        //console.log('database_data', database_data);

        try {
            const response = await fetch(url, {
                method: 'POST', // or 'PUT'
                body: JSON.stringify(database_data),
            });

            handleErrors(response);

            const data = await response.json();
            succeed(data);
        } catch (error) {
            fail(error);
        }
    };


    /* Display the first page of the gallery */
    createRequest('galleryPagination.php', paginationUISuccess, paginationUIError);

    // Add a new function to update the total count and refresh the pagination links
    const updateTotalCountAndPagination = async () => {
        await createRequest('getTotalCount.php', totalCountUISuccess, totalCountUIError);
    };

    database_data = {
        'category': category.value,
        'current_page': current_page,
        'per_page': per_page,
        'total_count': 0,
        'offset': offset
    };

    /*
     * Create an event listener to allow the user to change categories
     */

    // Update category.addEventListener
    category.addEventListener('change', () => {
        database_data.current_page = 1; // When changing category change current page to 1:
        database_data.category = category.value;

        // Close the lightbox if it's currently active
        if (lightbox.classList.contains('active')) {
            lightbox.classList.remove('active');
            document.getElementById('gallery_category').style.display = 'block';
            document.querySelector('.sidebar_pages').style.display = 'flex';
            document.querySelector('.container').style.display = 'grid';
        }

        updateTotalCountAndPagination(); // Call the new function to update the total count and refresh the pagination links
    }, false);

    const totalCountUISuccess = async (parsedData) => {
        database_data.total_count = await parsedData.total_count; // Total Pages of Category
        await createRequest('galleryPagination.php', paginationUISuccess, paginationUIError);
    };

    const totalCountUIError = (error) => {
        console.log("Database Table did not load", error);
    };

    document.addEventListener('DOMContentLoaded', () => {
        createRequest('getTotalCount.php', totalCountUISuccess, totalCountUIError);
    });
})();