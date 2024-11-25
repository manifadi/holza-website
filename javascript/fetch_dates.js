var holza_dates;
var currentEvents = 0;

async function getHolzaDates() {
    try {
      const response = await fetch('https://oton-agentur.at/caos/output.json');
      if (!response.ok) {
        throw new Error('Network response was not ok');
      }
      return response.json();
    } catch (error) {
      console.error('Fehler beim Lesen der JSON-Datei:', error);
      throw error; // Rethrow the error to handle it outside of the function if needed
    }
  }
  

  (async () => {
    try {
        var dates = await getHolzaDates();
        dates = Array.from(dates.artists_Tourdates);
        var filteredData = [];
        const todays_date = new Date();
        todays_date.setDate(todays_date.getDate()+1);
        dates.forEach(item => {
            if (item.artist_name.trim() == "HOLZA" && formatDate(item.date_event) >= todays_date) filteredData.push(item);
        });
        console.log(filteredData);
        holza_dates = filteredData;
        writeDates();
    } catch (error) {
      // Handle errors here
      console.error('An error occurred:', error);
    }
  })();

  function formatDate(date_string) {
    const dates = date_string.split(".");
    return new Date(dates[2], dates[1]-1, dates[0]);
  }

  function writeDates(){
    var oldEventCount = currentEvents;
    if(oldEventCount >= holza_dates.length-1) return;
    (currentEvents+4 > holza_dates.length-1) ? (currentEvents = holza_dates.length) : (currentEvents += 4);
    if(currentEvents >= holza_dates.length-1){
        document.getElementsByClassName("show-me-more")[0].classList.add("display-none");
    }
    const dateHTMLElement = document.querySelector("#termine");

    for (let i = oldEventCount; i < currentEvents; i++) {
        var item = holza_dates[i];
        const ticket_flex = document.createElement("div");
        ticket_flex.classList.add("ticket-flex");
    
            const time_location = document.createElement("div");
            time_location.classList.add("date-location");
    
                const concert_date = document.createElement("p");
                concert_date.innerHTML = item.date_event;

                const space_div = document.createElement("div");
                space_div.classList.add("devider");

    
                const location = document.createElement("div");
                location.classList.add("div-max-height");
                    const header_two = document.createElement("h2");
                        var event_location_string;
                        if(item.venue_name.includes("(")){
                            event_location_string = filterLocationString(item.venue_name.substring(0, item.venue_name.indexOf("(")), item.venue_city);
                        }else{
                            event_location_string = filterLocationString(item.venue_name, item.venue_city);
                        }

                    header_two.innerHTML = item.venue_city + ", <br>" + event_location_string;
                        const veranstalter_string = item.venue_name.substring(item.venue_name.indexOf("(")+1, item.venue_name.indexOf(")"));
                    const veranstalter = document.createElement("p");
                    veranstalter.innerHTML = veranstalter_string;
                location.appendChild(header_two);
                location.appendChild(veranstalter);
            
            time_location.appendChild(concert_date);
            time_location.appendChild(space_div);
            time_location.appendChild(location);
    
    
            const ticket_button = document.createElement("a");
            ticket_button.setAttribute('href', item.tickets_advancesale_internet);
            ticket_button.innerHTML = "Info/Tickets";
            ticket_button.classList.add("hover-mouse");
        
        ticket_flex.appendChild(time_location);
        ticket_flex.appendChild(ticket_button);
    
        dateHTMLElement.appendChild(ticket_flex);
    }
  }

  function filterLocationString(location_name, city_name){
    var location_name_array = location_name.split(" ");
    var temp_array = [];

    for(let i = 0; i < location_name_array.length; i++){
        if(location_name_array[i] != city_name && location_name_array[i].length > 0){
            temp_array.push(location_name_array[i]);
        }
    }
    var return_phrase = "";
    if(temp_array.length > 0){
        for(let i = 0; i < temp_array.length; i++){
            return_phrase += temp_array[i];
            if(i != temp_array.length-1){
                return_phrase += " ";
            }
        }
        return return_phrase;
    }
    return "";
  }