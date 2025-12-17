<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task 3 - Train Stations Database</title>
    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css">
    <!-- cdn for axios -->
    <script src="https://unpkg.com/axios/dist/axios.min.js"></script>
</head>
<body>
    <div id="app">

        <!-- tabs -->
        <div class="container">
            <div class="row">
                <div class="col text-center my-4">
                    <ul class="nav nav-tabs">
                        <li class="nav-item">
                            <a 
                                class="nav-link" 
                                :class="{ active: activeTab === 'stations' }" 
                                href="#" 
                                @click="activeTab = 'stations'">Stations</a>
                        </li>
                        <li class="nav-item">
                            <a 
                                class="nav-link" 
                                :class="{ active: activeTab === 'trains_schedule' }" 
                                href="#" 
                                @click="activeTab = 'trains_schedule'">Trains Schedule</a>
                        </li>
                        <li class="nav-item">
                            <a 
                                class="nav-link" 
                                :class="{ active: activeTab === 'train_details' }" 
                                href="#" 
                                @click="activeTab = 'train_details'">Train Details </a>

                        </li>
                    </ul>
                </div>
            </div>

        </div>
        
        <!-- trains schedule -->
        <div v-if="activeTab === 'trains_schedule'" class="d-flex flex-column align-items-center justify-content-center w-200">
            <h3 class="mb-4">Trains Schedule</h3>

            <div class="search-form" >
                <!-- search form -->
                <form @submit.prevent="searchTrains" class="d-flex gap-2 justify-content-center mb-4">
                    <input 
                        v-model="source" 
                        class="form-control w-25" 
                        type="text" 
                        placeholder="Source Station" 
                        list="departureStations"
                        @input="getStationsByKeyword(source)" />                    
                    <datalist id="departureStations">
                        <option v-for="station in suggestedStations" :value="station.station_code">
                           {{station.station_code}} - {{ station.station_name }}
                        </option>
                    </datalist>

                    <span class="mx-2">→</span>

                    <input 
                        v-model="destination" 
                        class="form-control w-25" 
                        type="text" 
                        placeholder="Destination Station" 
                        list="destinationStations" 
                        @input="getStationsByKeyword(destination)" />
                    <datalist id="destinationStations">
                        <option v-for="station in suggestedStations" :value="station.station_code">
                           {{station.station_code}} - {{ station.station_name }}
                        </option>
                    </datalist>

                    <button type="submit" class="btn btn-primary">Search Trains</button>
                </form>

            </div>
            
            <!-- results table -->
            <div class="mt-4 d-flex align-items-center justify-content-center flex-column w-100">
                <div id="train-results" class="w-75">
                    <div v-if="results.length === 0 && !selectedTrain" class="text-center text-muted">
                        <p>No trains found.</p>
                    </div>
                    <div v-else>
                        <div class="mb-3 d-flex justify-content-between align-items-center">
                            <p>Found : {{ totalResults }} train(s):</p>
                            <p>Records: {{ (pageNo - 1) * pageSize + 1 }} - {{ Math.min(pageNo * pageSize, totalResults) }}</p>
                        </div>
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>Train Number</th>
                                    <th>Train Name</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="train in results" :key="train.id" :id="train.id">
                                    <td>{{ train.train_no }}</td>
                                    <td>{{ train.train_name }}</td>
                                    <td>
                                        <button type="button" class="btn btn-primary" @click="displayTrainDetailsById(train.train_no)">Show Schedule</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <!-- train details modal -->
            <div v-if="selectedTrain">
                <div class="modal fade show d-block" id="trainDetails" tabindex="-1" aria-labelledby="trainDetailsLabel" aria-hidden="false" style="background-color: rgba(0,0,0,0.5);">
                    <div class="modal-dialog modal-dialog-centered modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="trainDetailsLabel">Train {{ selectedTrain.train_no }} - {{ selectedTrain.train_name }}</h5>
                                <button type="button" class="btn-close" @click="selectedTrain = null"></button>
                            </div>
                            <div class="modal-body">
                                <p><strong>Source:</strong> {{ selectedTrain.source_station_code }}</p>
                                <p><strong>Destination:</strong> {{ selectedTrain.destination_station_code }}</p>
                                <h6 class="mt-4">Full Schedule:</h6>
                                <div v-if="selectedTrain.schedule && selectedTrain.schedule.length > 0">
                                    <div v-for="(station, index) in selectedTrain.schedule" :key="index" class="container py-2">
                                        <div class="row g-0">
                                            <div class="col-sm-2 text-end pe-3 pt-1">
                                                <strong class="d-block">{{ station.arrival_time }}</strong>
                                                <small class="text-success">Arrival</small>
                                                <br/>
                                                <strong class="d-block mt-2">{{ station.departure_time }}</strong>
                                                <small class="text-danger">Departure</small>
                                            </div>
                                            <div class="col-auto position-relative">
                                                <div class="bg-black rounded-circle border border-white border-2 position-absolute start-50 translate-middle-x" style="width: 1rem; height: 1rem; top: 0.25rem; z-index: 1;"></div>
                                                <div class="h-100 border-start border-2 border-secondary position-absolute start-50 translate-middle-x"></div>
                                            </div>
                                            <div class="col ps-4 pb-5">
                                                <h5 class="fw-bold text-black">{{ station.station_code }}</h5>
                                                <p class="text-muted mb-0">Distance {{ station.distance }} km</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div v-else class="text-muted">No schedule available.</div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" @click="selectedTrain = null">Close</button>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- pagination -->
            <div v-if="totalResults > pageSize" class="d-flex gap-2 justify-content-center mb-4">
                <nav aria-label="Page navigation example">
                    <ul class="pagination">
                        <li class="page-item">
                            <a class="page-link" href="#" @click.prevent="prevPage">Previous</a>
                        </li>
                        <!-- 1st -->
                        <li>
                            <a 
                                v-if="pageNo > 2" 
                                class="page-link" 
                                href="#" 
                                @click.prevent="pageNo = 1; searchTrains()"
                                >
                                    1
                            </a>
                        </li>

                        <!-- middle -->
                        <li v-if=" pageNo > 1" class="page-item disabled active">
                            <span class="page-link">{{ pageNo }}</span>
                        </li>

                        <!-- last -->
                        <li>
                            <a 
                                v-if="totalResults > pageSize && totalResults > pageNo * pageSize" 
                                class="page-link" 
                                href="#" 
                                @click.prevent="pageNo = Math.ceil(totalResults / pageSize); searchTrains()"
                                >
                                    {{ Math.ceil(totalResults / pageSize) }}
                            </a>
                        </li>
                        <li class="page-item">
                            <a class="page-link" href="#" @click.prevent="nextPage">Next</a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>

        <div  v-if="activeTab === 'stations'" class="d-flex flex-column align-items-center justify-content-center w-200">
            <div class="d-flex flex-column align-items-center justify-content-center w-100 mt-4">
                <h3 class="mb-4">Train Stations</h3>
                <form @submit.prevent="searchStations" class="d-flex gap-3 justify-content-center mb-4">
                    <input 
                        v-model="stationSearch" 
                        class="form-control w-100" 
                        type="text" 
                        placeholder="Search Station Code"
                        list="stationSuggestions"
                        @input="getStationsByKeyword(stationSearch)" />
                    <datalist id="stationSuggestions">
                        <option v-for="station in suggestedStations" :value="station.station_code">
                           {{station.station_code}} - {{ station.station_name }}
                        </option>
                    </datalist>
                    <button type="submit" class="btn btn-primary">Search</button>
                </form>
                <div class="w-50">
                    <div v-if="stationResults.length === 0" class="text-center text-muted">
                        <p>No stations found.</p>
                    </div>
                    <div v-else>
                        <p>Found {{ totalResults }} station(s):</p>
                        <table class="table table-striped table-bordered w-100">
                            <thead>
                                <tr>
                                    <th>Station Number</th>
                                    <th>Station Name</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="station in stationResults" :key="station.code">
                                    <td>{{ station.train_number }}</td>
                                    <td>{{ station.train_name }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
        </div>

        <div v-if="activeTab === 'train_details'" >
            <h3 class="text-center my-4">Train Details</h3>
            <!-- find the train by id -->
             <form @submit.prevent="displayTrainCardDetails" class="d-flex gap-2 justify-content-center mb-4">
                <input 
                    v-model="trainIdSearch" 
                    class="form-control w-25" 
                    type="text" 
                    list="trainIdSuggestions"
                    @input="getTrainIdsByKeyword(trainIdSearch)"
                    placeholder="Enter Train ID" />
                <datalist id="trainIdSuggestions">
                    <option v-for="trainId in suggestedTrainIds"  :value="trainId"></option>
                </datalist>
                <button type="button" class="btn btn-primary" @click="displayTrainCardDetails(trainIdSearch)">Get Train Details</button>
            </form>
            <!-- card -->
            <div class="d-flex flex-column align-items-center justify-content-center w-100">
                <div class="card w-50">
                    <div class="card-body">
                        <div v-if="cardTrainDetails" class="d-flex flex-column container ">
                            <h5 class="card-title">Train {{ cardTrainDetails.train_number }} - {{ cardTrainDetails.train_name }}</h5>
                            <p class="card-text"><strong>Source:</strong> {{ cardTrainDetails.source_station_code }}</p>
                            <p class="card-text"><strong>Destination:</strong> {{ cardTrainDetails.destination_station_code }}</p>

                            <!-- get full train schedule -->
                            <h6 class="mt-4">Full Schedule:</h6>
                            <div v-if="cardTrainDetails.schedule && cardTrainDetails.schedule.length > 0" class="mb-3 left-90" >
                                <div v-for="(station, index) in cardTrainDetails.schedule" :key="index" class="container py-2">
                                    <div class="row g-0">
                                        <div class="col-sm-2 text-end pe-3 pt-1">
                                            <strong class="d-block">{{ station.arrival_time }}</strong>
                                            <small class="text-success">Arrival</small>
                                            <br/>
                                            <strong class="d-block mt-2">{{ station.departure_time }}</strong>
                                            <small class="text-danger">Departure</small>
                                        </div>
                                        <div class="col-auto position-relative">
                                            <div class="bg-black rounded-circle border border-white border-2 position-absolute start-50 translate-middle-x" style="width: 1rem; height: 1rem; top: 0.25rem; z-index: 1;"></div>
                                            <div class="h-100 border-start border-2 border-secondary position-absolute start-50 translate-middle-x"></div>
                                        </div>
                                        <div class="col ps-4 pb-5">
                                            <h5 class="fw-bold text-black">{{ station.station_name }}</h5>
                                            <p class="text-muted">{{ station.station_code }}</p>
                                            <p class="text-muted ">Distance {{ station.distance }} km</p>
                                        </div>
                                        </div>
                                    </div>
                                </div>
                                <div v-else class="text-muted">No schedule available.</div>
                            </div>
                            <div v-else class="text-center text-muted">
                                <p>No train details to display. Please enter a valid Train ID.</p>
                            </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>


<script>
    const app = Vue.createApp({
        data() {
            return {
                source: 'slo',
                destination: 'rjy',
                results: [],
                selectedTrain: null,
                activeTab: 'trains_schedule',
                stationSearch: '',
                stationResults: [],
                suggestedStations: [],
                suggestedTrainIds: [],
                cardTrainDetails: null,
                trainIdSearch: '',
                totalResults: 0,
                pageNo: 1,
                pageSize: 10

            }
        },
        mounted() {
            this.activeTab = 'trains_schedule';
        },
        methods: {
            searchTrains: async function() {
                const apiUrl = `get_stations_by_source_and_destination.php?source=${this.source}&destination=${this.destination}&page=${this.pageNo}`;
                const response = await axios.get(apiUrl);
                this.results = response.data.trains;
                this.totalResults = response.data.total_trains;

            },
            searchStations: async function() {
                const apiUrl = `get_trains_by_station.php?station_code=${this.stationSearch}`;
                const response = await axios.get(apiUrl);
                this.stationResults = response.data;

            },
            getStationsByKeyword: async function(keyword) {
                const response = await axios.get(`get_identical_stations.php?keyword=${keyword}`);
                const data = response.data;
                this.suggestedStations = data;
                return data;
            },
            displayTrainCardDetails: async function() {
                const train = await this.getTrainRowById(this.trainIdSearch);
                this.cardTrainDetails = train;
            },
            displayTrainDetailsById: async function(trainId) {
                const train = await this.getTrainRowById(trainId);
                this.selectedTrain = train;
            },
            getTrainRowById: async function(trainId) {
                const response = await axios.get(`get_train_by_id.php?train_id=${trainId}`);
                const data = response.data;
                return data;
            },
            getTrainIdsByKeyword: async function(keyword) {
                console.log("Fetching train IDs for keyword:", keyword);
                const response = await axios.get(`get_identical_trains_by_no.php?keyword=${keyword}`);
                const data = response.data;
                this.suggestedTrainIds = data.map(train => train.train_number);
                console.log("Suggested Train IDs:", this.suggestedTrainIds);
                return this.suggestedTrainIds;
            },
            nextPage() {
                this.pageNo += 1;
                this.searchTrains();
            },
            prevPage() {
                if (this.pageNo > 1) {
                    this.pageNo -= 1;
                }
                this.searchTrains();
            }
        }
    });
    app.mount('#app');
</script>

</html>
