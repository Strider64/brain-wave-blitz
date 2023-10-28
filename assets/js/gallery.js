'use strict';

class Gallery {
    constructor() {
        // DOM Elements
        this.category = document.querySelector('#category');
        this.container = document.querySelector('.container');
        this.sidebar = document.querySelector('.sidebar_pages');
        this.lightbox = document.querySelector('.lightbox');

        // Pagination Configurations
        this.current_page = 1;
        this.per_page = 4;
        this.offset = 0;
        this.total_pages = 0;

        // Data fetched from the database
        this.database_data = {
            'category': 'wildlife',
            'current_page': this.current_page,
            'per_page': this.per_page,
            'total_count': 0,
            'offset': this.offset
        };
        this.pages = [{}];

        // Binding `this` context to methods
        this.categoryUISuccess = this.categoryUISuccess.bind(this);
    }

    // Handle fetch errors
    handleErrors(response) {
        if (!response.ok) {
            throw (response.status + ' : ' + response.statusText);
        }
        return response;
    }

    // Update the UI upon successfully fetching gallery category data
    async categoryUISuccess(parsedData) {
        this.clearContainer();

        parsedData.forEach((slide, index) => {
            this.createSlide(slide, index);
        });

        this.addImageListeners();
        this.addLightboxListener();
    }

    // Clear all child elements of the container
    clearContainer() {
        while (this.container.firstChild) {
            this.container.removeChild(this.container.firstChild);
        }
    }

    // Create individual gallery slides
    createSlide(slide, count) {
        const displayFormat = [
            "gallery-container w-3 h-2",
            'gallery-container w-3 h-2',
            'gallery-container w-3 h-2',
            'gallery-container w-3 h-2',
            'gallery-container w-3 h-2',
            'gallery-container w-3 h-2'
        ];

        const displayDiv = this.createElementWithClass('div', displayFormat[count]);
        this.container.appendChild(displayDiv);

        const galleryItem = this.createElementWithClass('div', 'gallery-item');
        displayDiv.appendChild(galleryItem);

        const images = this.createElementWithClass('div', 'images');
        galleryItem.appendChild(images);

        const galleryImage = this.createElement('img', {
            src: slide.image_path,
            alt: slide.content,
            loading: 'lazy',
            'data-exif': `${slide.Model} ${slide.ExposureTime} ${slide.Aperture} ${slide.ISO} ${slide.FocalLength}`
        });
        images.appendChild(galleryImage);

        const paragraph = this.createElementWithClassAndContent('p', 'hideContent', slide.content);
        images.appendChild(paragraph);

        const title = this.createElementWithClass('div', 'title');
        galleryItem.appendChild(title);

        const heading1 = this.createElementWithClassAndContent('h1', 'pictureHeading', `${slide.heading[0].toUpperCase()}${slide.heading.slice(1)}`);
        title.appendChild(heading1);

        const titleSpan = this.createElementWithClassAndContent('span', 'exifInfo', slide.Model);
        title.appendChild(titleSpan);
    }

    // Utility function to create an HTML element with attributes
    createElement(tag, attributes = {}) {
        const element = document.createElement(tag);
        Object.entries(attributes).forEach(([key, value]) => {
            element.setAttribute(key, value);
        });
        return element;
    }

    createElementWithClass(tag, className) {
        const element = this.createElement(tag);  // Corrected this line
        element.className = className;
        return element;
    }

    createElementWithClassAndContent(tag, className, content) {
        const element = this.createElementWithClass(tag, className); // Corrected this line
        element.textContent = content;
        return element;
    }

    // Add click event listeners to images to open in lightbox
    addImageListeners() {
        const images = document.querySelectorAll('img');
        images.forEach(image => {
            image.addEventListener('click', () => this.handleImageClick(image)); // Corrected this line
        });
    }

    // Handle the click event when an image is clicked
    handleImageClick(image) {
        // Your logic when an image is clicked,
        // e.g., showing the lightbox, adding the image and text, etc.
        this.lightbox.classList.add('active');
        this.container.style.display = 'none';

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
        console.log('image', image);
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
        while (this.lightbox.firstChild) {
            this.lightbox.removeChild(this.lightbox.firstChild)
        }

        // Add the container to the lightbox
        this.lightbox.appendChild(imageExifContainer);

        /* Add Content to Screen */
       this.lightbox.appendChild(galleryText);
    }

    // Add click event listener to lightbox to close it
    addLightboxListener() {
        this.lightbox.addEventListener('click', () => {
            if (this.lightbox.hasChildNodes()) {
                this.exitLightbox();
            }
        });
    }

    // Close the active lightbox
    exitLightbox() {
        document.getElementById('gallery_category').style.display = 'block';
        document.querySelector('.sidebar_pages').style.display = 'flex';

        this.lightbox.classList.remove('active');
        this.lightbox.classList.add('lightbox');

        document.querySelector('.container').style.display = 'grid';
        // document.querySelector('.pagination').style.display = 'flex';
    }

    // Handle errors when fetching gallery category data fails
    categoryUIError(error) {
        console.log("Database Table did not load", error);
    }

    // Send a request to the server to fetch images
    async createImageRequest(url, succeed, fail) {
        // ... The logic of the createImageRequest function
        try {
            const response = await fetch(url, {
                method: 'POST', // or 'PUT'
                body: JSON.stringify(this.database_data),
            });

            this.handleErrors(response);

            const data = await response.json();
            succeed(data);
        } catch (error) {
            fail(error);
        }
    }

    // Clear all pagination links
    restLinks() {
        /* Remove Links For Screen (cleanup) */

        while (this.sidebar.firstChild) {
            this.sidebar.removeChild(this.sidebar.firstChild)
        }
    }

    // Update the UI with the received pagination data
    async paginationUISuccess(parsedData) {
        this.restLinks();
       // console.log('parsed data', parsedData);
       // console.log('total_count - Parsed Data', this.database_data.total_count);
        this.database_data.offset = await parsedData.offset;
        this.total_pages = Math.ceil(this.database_data.total_count / this.database_data.per_page);

        /* Create the Display Links and add an event listener */
        this.pages = [{}];
        /*
         * Creating the array of page object(s)
         */
        for (let x = 0; x < this.total_pages; x++) {
            this.pages[x] = {page: x + 1};
        }

        this.pages.forEach(link_page => {
            const links = document.createElement('div');
            links.className = 'links';
            this.sidebar.appendChild(links);
            /*
             * Add event listener for the links
             */
            links.addEventListener('click', () => {
                this.database_data.current_page = link_page.page;

                // Close the lightbox if it's currently active
                if (this.lightbox.classList.contains('active')) {
                    this.lightbox.classList.remove('active');
                    document.getElementById('gallery_category').style.display = 'block';
                    document.querySelector('.sidebar_pages').style.display = 'flex';
                    document.querySelector('.container').style.display = 'grid';
                }

                this.createRequest('galleryPagination.php', this.paginationUISuccess, this.paginationUIError);
            });

            const pageText = document.createElement('p');
            pageText.className = 'linkStyle';
            pageText.id = 'page_' + link_page.page;
            pageText.textContent = link_page.page;
            links.appendChild(pageText);
            if (this.database_data.current_page === link_page.page) {
                links.style.backgroundColor = "#00b28d";
            }
        })

        await this.createImageRequest('galleryImagesGet.php', this.categoryUISuccess, this.categoryUIError);
    }


    // Handle errors when fetching pagination data fails
    paginationUIError(error) {
        console.log("Database Table did not load", error);
    }

    // Send a request to the server
    async createRequest(url, succeed, fail) {
        // ... The logic of the createRequest function
        try {
            const response = await fetch(url, {
                method: 'POST', // or 'PUT'
                body: JSON.stringify(this.database_data),
            });

            this.handleErrors(response);

            const data = await response.json();
            console.log('count', data);
            succeed(data);
        } catch (error) {
            fail(error);
        }
    }

    // Send a request to get the total number of images in a category
    async updateTotalCountAndPagination() {
        await this.createRequest('getTotalCount.php', this.totalCountUISuccess.bind(this), this.totalCountUIError.bind(this));
    }

    // Update the UI upon successfully fetching the total count
    totalCountUISuccess(parsedData) {
        this.database_data.total_count = parsedData.total_count;
        this.createRequest('galleryPagination.php', this.paginationUISuccess.bind(this), this.paginationUIError.bind(this));
    }

    // Handle errors when fetching the total count fails
    totalCountUIError(error) {
        console.log("Database Table did not load", error);
    }

    // Add event listeners to DOM elements
    bindEvents() {
        this.category.addEventListener('change', () => {
            this.database_data.current_page = 1;
            this.database_data.category = this.category.value;

            if (this.lightbox.classList.contains('active')) {
                this.lightbox.classList.remove('active');
                document.getElementById('gallery_category').style.display = 'block';
                document.querySelector('.sidebar_pages').style.display = 'flex';
                document.querySelector('.container').style.display = 'grid';
            }

            this.updateTotalCountAndPagination();
        });

        document.addEventListener('DOMContentLoaded', () => {
            this.createRequest('galleryPagination.php', this.paginationUISuccess.bind(this), this.paginationUIError.bind(this));
        });
    }

    // Initialization function
    init() {
        this.updateTotalCountAndPagination();
        this.bindEvents();
    }
}

const gallery = new Gallery();
gallery.init();
