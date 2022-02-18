// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * format_buttons_renderer
 *
 * @package    format_buttons
 * @author     Rodrigo Brandão <https://www.linkedin.com/in/brandaorodrigo>
 * @copyright  2020 Rodrigo Brandão <rodrigo.brandao.contato@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

M.format_buttons = M.format_buttons || {
    ourYUI: null,
    numsections: 0
};

M.format_buttons.init = function(Y, numsections, currentsection, courseid) {
    this.ourYUI = Y;
    this.numsections = parseInt(numsections);
    /* document.getElementById('buttonsectioncontainer').style.display = 'table'; */

    window.onload = function main() {
        var slider = new Slider(document.querySelector('#slider'),{
            items: this.numsections,
            responsive: true,
            mobile : {
                items : 2
            },
            tablet : {
                width : 768,
                items : 5,
            },
            desktop : {
                width : 1040,
                items : 8,
            },
            deviceChanged : function (e) {
                console.log(e);
            },
            navChanged : function (e) {
                console.log(e);
            }
        })

        document.addEventListener('DOMContentLoaded', main)
    }
    var extend = function (source, target) {
        if(typeof source == 'object'){
            for (var property in source){
                if(!target[property]) {
                    target[property] = source[property];
                }
            }
        }
        return target;
    }

    function Slider(el, options) {
        var self = this,
            carousel_container,
            slides,
            prev_button,
            next_button,
            carousel_nav,
            current_nav,
            steps,
            current_step,
            item_per_step,
            /* play_timer,
            resize_timer, */
            windowState,
            listeners = [];


        var defaults = {
            /* autoplay: false,
            interval: 4000, */
            createNav: true,
            items: this.numsections,
            responsive: false,
            mobile: {
            items: 2
            },
            tablet: {
            width: 768,
            items: 6
            },
            desktop: {
            width: 1024,
            items: 8
            },
            maxHeight: undefined,
            deviceChanged: undefined,
            navchanged: undefined
        };

        // wrap function
        function wrap(element, start, end) {
            var html = start + element.outerHTML + end;
            element.outerHTML = html;
        }

        // Create Navigation under carousel
        function create_nav() {
            carousel_nav = document.createElement('ul');
            carousel_nav.classList.add('carousel__nav');
            create_nav_links();
            el.appendChild(carousel_nav);
        }

        // Update the carousel nav 
        function update_nav() {
            var links_count = carousel_nav.querySelectorAll('li').length;
            if (links_count !== steps) {
              carousel_nav.innerHTML = '';
              create_nav_links();
              raiseEvent({
                type: 'navchanged',
                currentStep: current_step,
                itemPerStep: item_per_step,
                steps: steps
              });
            }
        }
        
        //Get index for a specific element in carousel
        function getIndex(element) {
            var nodes = element.parentNode.childNodes;
            for (var i = 0; i < nodes.length; i++) {
              if (nodes[i] == element) {
                return i;
              }
            }
            return -1;
        }

        //Create the Navigation Link
        function create_nav_links() {
            for (var i = 0; i < steps; i++) {
              var li = document.createElement('li');
              var nav_link = document.createElement('a');
              nav_link.setAttribute('href', '#');
        
              nav_link.addEventListener('click', function(e) {
                e.preventDefault();
        
                var index = getIndex(this.parentElement);
                moveTo(index);
        
              });
              li.appendChild(nav_link);
              carousel_nav.appendChild(li);
            }
        }

        //Get The Current left Slide
        function get_current_left_slides() {

            if (current_step == 0) {
              return 0;
            } else if (current_step == steps - 1) {
              var previous_step_slide_total = (current_step - 1) * item_per_step, // 8
                slides_left = (slides.length - current_step * item_per_step), // 1
                result = previous_step_slide_total + slides_left; // --> 9
                console.log('Step', steps);
                console.log('current step', current_step);
                console.log('previous step slide total', previous_step_slide_total);
                console.log('slides left', slides_left);
                console.log('result', result);
              return result;
            } else {
              var result = current_step * item_per_step;
              return result;
            }
        }

        //Get slide to move back of forward
        function get_slides_to_move(step) {

            // back
            if (step < current_step) {
        
              if (step == steps - 2) {
                // ex slides.length  = 13 | steps 4 | step 2
                var slide_total = slides.length, // 25
                  slides_left = (slides.length - step * item_per_step), // 7
                  slides_to_move = slide_total - slides_left; // --> 18
                console.log('slide total', slide_total);
                console.log('slide left', slides_left);
                console.log('slide to move', slides_to_move);
                return slides_to_move;
              } else {
                console.log('Step * item_per_step back', step * item_per_step);
                return step * item_per_step;
              }
        
            }
            // forward
            if (step > current_step) {
        
              if (step == steps - 1) {
                // ex slides.length  = 13 | steps 4 | step 3
                var previous_step_slide_total = (step - 1) * item_per_step, // 8
                slides_left = (slides.length - step * item_per_step), // 1
                slides_to_move = previous_step_slide_total + slides_left; // --> 9
                console.log('Slides to move forward', slides_to_move);

                return slides_to_move;
              } else {
                console.log('Step * item_per_step forward', step * item_per_step);
                return step * item_per_step;
              }
        
            }
        
            return 0;
        }

        // Resize the Carousel items
        function onResize() {
                    
            // repositionne les slides sans animation
            var percentage = 100 / item_per_step,
              x = item_per_step > 1 ?
              percentage * get_current_left_slides() :
              current_step * percentage;
        
            carousel_container.style.transition = 'none';
            carousel_container.style.transform = 'translateX(-' + x + '%)';
            update_nav();
            update_buttons();
            setTimeout(function() {
              carousel_container.style.removeProperty('transition');
            }, 500);
        
            if (window.innerWidth < options.tablet.width) {
              if (windowState != 'mobile') {
                windowState = 'mobile';
                if (options.responsive) setItems(options.mobile.items);
                raiseEvent({
                  type: 'devicechanged',
                  device: 'mobile'
                });
              }
            } else if (window.innerWidth < options.desktop.width) {
              if (windowState != 'tablet') {
                windowState = 'tablet';
                if (options.responsive) setItems(options.tablet.items);
                raiseEvent({
                  type: 'devicechanged',
                  device: 'tablet'
                });
              }
            } else if (window.innerWidth > options.desktop.width) {
              if (windowState != 'desktop') {
                windowState = 'desktop';
                if (options.responsive) setItems(options.desktop.items);
                raiseEvent({
                  type: 'devicechanged',
                  device: 'desktop'
                });
              }
            }
        }
        // Update buttons
        function update_buttons() {

            if (prev_button) {
              if (current_step <= 0) {
                prev_button.classList.add('hidden');
              } else {
                prev_button.classList.remove('hidden');
              }
            }
        
            if (next_button) {
              if (current_step >= steps - 1) {
                next_button.classList.add('hidden');
              } else {
                next_button.classList.remove('hidden');
              }
            }
        
            // navs
            if (options.createNav) {
              current_nav = carousel_nav.querySelectorAll('a')[current_step];
              var active = carousel_nav.querySelector('a.active');
              if (active) {
                active.classList.remove('active');
              }
              current_nav.classList.add('active');
            }
          }
        // Raise an Event
        function raiseEvent(event) {
            if (typeof event == 'string') event = {
              type: event
            };
            if (!event.target) event.target = this;
            if (!event.type) throw new Error("Event object missing 'type' property.");
        
            if (listeners[event.type] instanceof Array) {
              var _listeners = listeners[event.type];
              for (var i = 0; i < _listeners.length; i++) {
                _listeners[i].call(self, event);
              }
            }
        }

        //Init function
        function init() {

            if (!el) throw new Error("'El' cannot be null");
        
            options = extend(defaults, options || {});
        
            wrap(el.querySelector('.carousel-inner'), '<div class="carousel__viewport">', '</div>');
        
            carousel_container = el.querySelector('.carousel-inner'); // .slides__slides = carousel-inner
        
            slides = el.querySelectorAll('.buttonsection');
        
            prev_button = el.querySelector('.carousel__button--prev');
            next_button = el.querySelector('.carousel__button--next');
        
            if (options.maxHeight) {
              carousel_container.style.maxHeight = options.maxHeight + 'px';
            }
        
            if (options.deviceChanged) {
              addListener('devicechanged', options.deviceChanged);
            }
        
            if (options.navChanged) {
              addListener('navchanged', options.navChanged);
            }
        
            current_step = 0;
        
            if (options.responsive) {
              if (window.innerWidth < options.tablet.width) {
                setItems(options.mobile.items);
              } else if (window.innerWidth < options.desktop.width) {
                setItems(options.tablet.items);
              } else if (window.innerWidth > options.desktop.width) {
                setItems(options.desktop.items);
              }
            } else {
              setItems(options.items);
            }
        
            if (options.createNav) {
              create_nav();
            }
        
            // events
            window.addEventListener('resize', function() {
              onResize();
            });
        
            if (prev_button) {
              prev_button.addEventListener('click', function(e) {
                e.preventDefault();
                moveBack();
              });
            }
        
            if (next_button) {
              next_button.addEventListener('click', function(e) {
                  console.log('E in forward', e);
                e.preventDefault();
                moveForward();
              });
            }
        
            //
            //onResize();
            update_buttons();
        }

        // AddListener function
        function addListener(type, listener) {
            if (typeof listeners[type] == "undefined") {
              listeners[type] = [];
            }
            listeners[type].push(listener);
        }

        // Move back function
        function moveBack() {
            if (current_step <= 0) return false;
            
            moveTo(current_step - 1);
        }
        
        // Move Forward 
        function moveForward() {
            console.log('Move forward current section', currentsection);
            console.log('Move forward current step', current_step);

            // console.log('Move forward current step', slides);

            if (current_step >= steps - 1) return false;
            

            moveTo(current_step + 1);

            if (currentsection <= slides.length) {
                console.log('Foward',current_step );
                M.format_buttons.show(currentsection + 1, courseid); 
            
                
            }
        }

        // Set items in carousel visible
        function setItems(count) {

            item_per_step = count;
            steps = Math.ceil(slides.length / item_per_step);
            console.log('item per step One', item_per_step);
            
            if (item_per_step > 1) {
                console.log('item per step 2', item_per_step);
              var slide_width = 100 / item_per_step;
              for (var i = 0; i < slides.length; i++) {
                var slide = slides[i];
                if (slide) {
                    console.log('item per step 22', item_per_step);
                  slide.style.width = slide_width + '%';
                }
              }
            } else {
              console.log('item per step 3', item_per_step);
              console.log('Slides length', slides.length);
              for (var i = 0; i < slides.length; i++) {
                var slide = slides[i];
                if (slide) {
                  slide.style.removeProperty('width');
                }
              }
            }
        
            // repositionne les slides sans animation
            var percentage = 100 / item_per_step,
              x = item_per_step > 1 ?
              percentage * get_current_left_slides() :
              current_step * percentage;
        
            carousel_container.style.transition = 'none';
            carousel_container.style.transform = 'translateX(-' + x + '%)';
            setTimeout(function() {
                carousel_container.style.removeProperty('transition');
            }, 500);
        }

        //
        function moveTo(step) {
            if (step < 0 || step > steps - 1) return false;
        
            var percentage = 100 / item_per_step,
              x = item_per_step > 1 ?
              percentage * get_slides_to_move(step) :
              step * percentage;
        
            carousel_container.style.transform = 'translateX(-' + x + '%)';
            current_step = step;
            
            console.log('Current step in Move to', current_step);
            update_buttons();
        }

        init();

        return {
            addEventListener: addEventListener,
            moveBack: moveBack,
            moveForward: moveForward,
            moveTo: moveTo,
            /* pause: pause,
            play: play, */
            setItems: setItems
          }
    }
    // >>> End Slider function

    // >>> Begin Map Config
    /* var map = L.map('map_id', {
      maxZoom: 18,
      }).setView([[51.505, -0.09], 7]);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
       attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map); */
    // <<<< End Map Config

    // Bottom button variable
    const next_bottom = document.querySelector('.bottom_next');
    const prev_bottom = document.querySelector('.bottom_previous');
    const home_bottom = document.querySelector('.bottom_home');
    /* if (prev_button) {
      if (current_step <= 0) {
        prev_button.classList.add('hidden');
      } else {
        prev_button.classList.remove('hidden');
      }
    }

    if (next_button) {
      if (current_step >= steps - 1) {
        next_button.classList.add('hidden');
      } else {
        next_button.classList.remove('hidden');
      }
    } */
    // Next_bottom code come here...
    next_bottom.addEventListener('click', () => {
        if (currentsection >= this.numsections -1) {
          next_bottom.classList.add('remove_pointer');
           
        } else {
          next_bottom.classList.remove('remove_pointer');
          M.format_buttons.show(currentsection + 1, courseid);
          currentsection++;
        }
        // next_bottom.classList.add('hidden');
        
        console.log("Current Section next", currentsection);
       // currentsection++;
         
    });
    // Previous_bottom code here ...
    prev_bottom.addEventListener('click', () => {
        if (currentsection <= 1) {
          prev_bottom.classList.add('remove_pointer');
            
        } else {
          prev_bottom.classList.remove('remove_pointer');
          M.format_buttons.show(currentsection - 1, courseid);
          currentsection--;
        }
       
        console.log("Current Section Previous", currentsection);
        // currentsection--; 
    });
    // Home_bottom code come here...
    home_bottom.addEventListener('click', () => {
        location.assign('http://localhost/moodle/course/format/buttons/infos.php?id=' + courseid);
    });
    var findHash = function (href) {
        var id = null;
        if (href.indexOf('#section-') !== 0) {
            var split = href.split('#section-');
            id = split[1];
        }
        return id;
    };

    var hash = findHash(window.location.href);
    if (hash) {
        currentsection = hash;
    }

    if (currentsection) {
        M.format_buttons.show(currentsection, courseid);
    }

    Y.delegate('click', function (e) {
        var href = e.currentTarget.get('href');
        currentsection = findHash(href);
        M.format_buttons.show(currentsection, courseid)
    }, '[data-region="drawer"]', '[data-type="30"]');

};

M.format_buttons.hide = function() {
    for (var i = 1; i <= this.numsections; i++) {
        if (document.getElementById('buttonsection-' + i) != undefined) {
            var buttonsection = document.getElementById('buttonsection-' + i);
            buttonsection.setAttribute('class', buttonsection.getAttribute('class').replace('sectionvisible', ''));
            document.getElementById('section-' + i).style.display = 'none';
        }
    }
};

M.format_buttons.show = function(id, courseid) {
    this.hide();
    if (id > 0) {
        var buttonsection = document.getElementById('buttonsection-' + id);
        var currentsection = document.getElementById('section-' + id);
        if (buttonsection && currentsection) { // && currentsection
            buttonsection.setAttribute('class', buttonsection.getAttribute('class') + ' sectionvisible');
            currentsection.style.display = 'block';
            document.cookie = 'sectionvisible_' + courseid + '=' + id + '; path=/';
            M.format_buttons.h5p();
        }
    }
};

M.format_buttons.h5p = function() {
    window.h5pResizerInitialized = false;
    var iframes = document.getElementsByTagName('iframe');
    var ready = {
        context: 'h5p',
        action: 'ready'
    };
    for (var i = 0; i < iframes.length; i++) {
        if (iframes[i].src.indexOf('h5p') !== -1) {
            iframes[i].contentWindow.postMessage(ready, '*');
        }
    }
};
