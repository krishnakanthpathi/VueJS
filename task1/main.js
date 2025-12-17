
const importTrainsCSV = async () => {
    const response = await fetch('trains_dataset.csv');
    const csvText = await response.text();

    const data = csvText.split('\n')
    const rows = [];
    /*
       Train No,   Train Name ,SEQ ,Station Code,Station Name,Arrival time,Departure Time,Distance,Source Station,Source Station Name,Destination Station,Destination Station Name
        107,    SWV-MAO-VLNK,   1 ,  SWV, SAWANTWADI R,00:00:00,10:25:00,0,SWV,SAWANTWADI ROAD,MAO,MADGOAN JN.
    */
    for (let i = 1; i < data.length; i++) {
        const row = data[i].split(',');
        const rowObj = {
            id: row[0],
            train_number: row[0],
            train_name: row[1],
            SEQ: row[2],
            station_code: row[3],
            station_name: row[4],
            arrival_time: row[5],
            departure_time: row[6],
            distance: row[7],
            source_station: row[8],
            source_station_name: row[9],
            destination_station: row[10],
            destination_station_name: row[11],
            
        }
        // validate row length
        if (!rowObj.train_number || !rowObj.source_station || !rowObj.destination_station) continue;
        rows.push(rowObj);
    
    }
    return rows;
}

const searchTrains = async (departure, destination) => {
    let allTrains = await importTrainsCSV();
    const results = [];

    
    departure = departure.toUpperCase().trim();
    destination = destination.toUpperCase().trim();
    
    const srcMap = new Map(); // train_id -> source SEQ
    const dstMap = new Map(); // train_id -> destination SEQ

    for (const train of allTrains) {
        if (train.station_code === departure) {
            srcMap.set(train.train_number, parseInt(train.SEQ));
        }
        if (train.station_code === destination) {
            dstMap.set(train.train_number, parseInt(train.SEQ));
        }
    }
    // console.log('Source Map:', srcMap);
    // console.log('Destination Map:', dstMap);

    // console.log('All Trains:', allTrains);
    
    // 3. Intersect and Validate (Source SEQ < Dest SEQ)
    for(const [trainNumber, srcSeq] of srcMap.entries()) {

        if (dstMap.has(trainNumber)) {
            const dstSeq = dstMap.get(trainNumber);
            if (!(srcSeq < dstSeq)) {
                const trainInfo = allTrains.find(t => parseInt(t.train_number) == parseInt(trainNumber));
                results.push(trainInfo);
            }
        }
    }

    return results;

}

const loadAllTrains = async () => {
    const trains = await importTrainsCSV();
    const resultsDiv = document.getElementById('all-trains-results');
    resultsDiv.innerHTML = '';
    const table = document.createElement('table');
    const thead = document.createElement('thead');
    const tbody = document.createElement('tbody');
    thead.innerHTML = `
        <tr>
            <th>ID</th>
            <th>Train Number</th>
            <th>Train Name</th>
            <th>SEQ</th>
            <th>Station Code</th>
            <th>Station Name</th>
            <th>Arrival Time</th>
            <th>Departure Time</th>
            <th>Distance</th>
            <th>Source Station</th>
            <th>Source Station Name</th>
            <th>Destination Station</th>
            <th>Destination Station Name</th>
        </tr>
    `;
    table.appendChild(thead);
    // Populate tbody for 100 trains
    trains.slice(0, 100).forEach(train => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td>${train.id}</td>
            <td>${train.train_number}</td>
            <td>${train.train_name}</td>
            <td>${train.SEQ}</td>
            <td>${train.station_code}</td>
            <td>${train.station_name}</td>
            <td>${train.arrival_time}</td>
            <td>${train.departure_time}</td>
            <td>${train.distance}</td>
            <td>${train.source_station}</td>
            <td>${train.source_station_name}</td>
            <td>${train.destination_station}</td>
            <td>${train.destination_station_name}</td>
        `;
        tbody.appendChild(tr);
    });
    table.appendChild(tbody);
    resultsDiv.appendChild(table);
    // Add Bootstrap classes for styling
    table.classList.add('table', 'table-striped', 'table-bordered');
}

const getTrainRowById = async (trainId) => {
    const trains = await importTrainsCSV();
    const train = trains.find(t => parseInt(t.train_number) === parseInt(trainId));
    if (!train) return null;
    return train;
}


const getStationByTrainId = async (trainId) => {
    const trains = await importTrainsCSV();
    const stations = trains.filter(t => parseInt(t.train_number) === parseInt(trainId));
    stations.sort((a, b) => parseInt(a.SEQ) - parseInt(b.SEQ));
    return stations;
}


const createTrainInfoCard = async (trainId) => {
    const trainRow = await getTrainRowById(trainId);
    if (!trainRow) return '<p>No details found.</p>';
    return `
        <div class="train-details p-4 mb-3">
            <h5 class="card-title fw-bold">${trainRow.train_number} - ${trainRow.train_name}</h5>
            <div class="details-grid">
                <p><strong>Station Code:</strong> <span class="badge bg-secondary">${trainRow.station_code}</span></p>
                <p><strong>Station Name:</strong> ${trainRow.station_name}</p>
                <p><strong>Source:</strong> ${trainRow.source_station} - ${trainRow.source_station_name}</p>
                <p><strong>Destination:</strong> ${trainRow.destination_station} - ${trainRow.destination_station_name}</p>
            </div>
            <hr/>
        </div>
    `;
}

const createTrainTimeline = (train) => {
    return `
        <div class="container py-2">
                <div class="row g-0">
                    <div class="col-sm-2 text-end pe-3 pt-1">
                        <strong class="d-block">${train.arrival_time}</strong>
                        <small class="text- text-success">Arrival</small>
                        <br/>
                        <strong class="d-block mt-2">${train.departure_time}</strong>
                        <small class="text- text-danger">Departure</small>
                    </div>
                    <div class="col-auto position-relative">
                        <div class="bg-black rounded-circle border border-white border-2 position-absolute start-50 translate-middle-x" 
                            style="width: 1rem; height: 1rem; top: 0.25rem; z-index: 1;">
                        </div>
                        <div class="h-100 border-start border-2 border-secondary position-absolute start-50 translate-middle-x"></div>
                    </div>
                    <div class="col ps-4 pb-5">
                        <h5 class="fw-bold text-black">${train.station_name}</h5>
                        <p class="text-muted mb-0">Distance ${train.distance} km</p>
                    </div>
                </div>
                
            </div>
    `;
}

const displayModalDetails =  async (trainId) => {
    const modalBody = document.getElementById('modal-body');
    const trainRow = await getTrainRowById(trainId);
    const fullSchedule = await getStationByTrainId(trainId);

    if (trainRow) {
        modalBody.innerHTML = await createTrainInfoCard(trainId);
        
        // Full Schedule Table
        modalBody.innerHTML += `
               <h6>Full Schedule:</h6>
            `;
        for(let train of fullSchedule) {
            
            modalBody.innerHTML += createTrainTimeline(train);
        }
    } else {
        modalBody.innerHTML = '<p>No details found.</p>';
    }
}

const createTrainsTableElement = (trains) => {
    const table = document.createElement('table');
    const thead = document.createElement('thead');
    const tbody = document.createElement('tbody');

    thead.innerHTML = `
        <tr>
            <th>Train Number</th>
            <th>Train Name</th>
            <th>Actions</th>
        </tr>
    `;
    table.appendChild(thead);
    trains.forEach(train => {
        const tr = document.createElement('tr');
        // Adding id to tr 
        tr.id = `${train.id}`;
        tr.innerHTML = `
            <td>${train.train_number}</td>
            <td>${train.train_name}</td>
            <td>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#trainDetails" onclick="displayModalDetails(${train.id})">Show Schedule</button> 
            </td>
        `;
        tbody.appendChild(tr);
    });
    table.appendChild(tbody);
    return table;
}

const displayResults = (results) => {
    const resultsDiv = document.getElementById('train-results');
    resultsDiv.innerHTML = '';
    if (results.length === 0) {
        resultsDiv.innerHTML = '<p>No trains found.</p>';
        return;
    }
    resultsDiv.innerHTML = `<p>Found ${results.length} train(s):</p>`;

    const table = createTrainsTableElement(results);
    resultsDiv.appendChild(table);
    // Add Bootstrap classes for styling
    table.classList.add('table', 'table-striped', 'table-bordered');

}

const getTrainStationCodes = async () => {
    let allTrains = await importTrainsCSV();
    const stationCodes = new Set();
    for (const train of allTrains) {
        stationCodes.add(train.station_code);
    }
    return Array.from(stationCodes).sort();
}

const populateStationCodes = async () => {
    const stationCodes = await getTrainStationCodes();
    const departureSelect = document.getElementById('departureStations');
    const destinationSelect = document.getElementById('destinationStations');
    stationCodes.forEach(code => {
        const option1 = document.createElement('option');
        option1.value = code;
        option1.text = code;
        departureSelect.appendChild(option1);

        const option2 = document.createElement('option');
        option2.value = code;
        option2.text = code;
        destinationSelect.appendChild(option2);
    });
}

const displayTrainDetails = async (trainId) => {
    const trainDetailsDiv = document.getElementById('train-details');
    const trainRow = await getTrainRowById(trainId);
    if (trainRow) {
        trainDetailsDiv.innerHTML = createTrainInfoCard(trainId);
        trainDetailsDiv.innerHTML += `<h5>Full Schedule:</h5>`;
        const fullSchedule = await getStationByTrainId(trainId);
        for(let train of fullSchedule) {
            trainDetailsDiv.innerHTML += createTrainTimeline(train);
        }
    } else {
        trainDetailsDiv.innerHTML = '<p>No details found.</p>';
    }
    
}


const TrainSeachForm = document.getElementById('train-search-form');


TrainSeachForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    const departure = e.target.departure.value;
    const destination = e.target.destination.value;
    const results = await searchTrains(departure, destination);
    console.log(results);
    displayResults(results);
});




populateStationCodes();

/*
// issues faced:
filtering the trains based on source and destination stations on dynamic input


*/