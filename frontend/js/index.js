// show more matches button
const moreMaches = document.querySelector('.more-matches');
moreMaches.addEventListener('click', (e)=>{
    const xhr = new XMLHttpRequest();
    xhr.open('get', 'ajax.php?type=more');
    xhr.addEventListener('load', function () { 
        document.querySelector('#more-matches-output').innerHTML = listMoreMatches(this.response, [...teams][0]);
    });
    xhr.responseType = 'json';
    xhr.send(null);
    moreMaches.style.display = "none";
});

const favMatches = document.querySelector('.liked-teams-matches');
let favMatchesOutput = [];
favMatches.addEventListener('click', (e)=>{
    location.href='./show_matches.php?type=fav' ;
    // const xhr = new XMLHttpRequest();
    // xhr.open('get', 'ajax.php?type=fav');
    // xhr.addEventListener('load', function () { 
    //     const matches = listMoreMatches(this.response, [...teams][0]);
    // });
    // xhr.responseType = 'json';
    // xhr.send(null);
});

// helper functions 
let data = [];
export function getTeams(){
    getRandomUser()
    return data;
}
async function getRandomUser(){
  const response = await fetch('./../backend/data/teams.json');
  data.push(await response.json());
}
export const favMatchesO = ()=> {return favMatchesOutput}; 

const teams = getTeams();

function getTeamName(id, teams){
    for(let teamId in teams){
        if(teams[teamId]['id'] == id) return teams[teamId]['name'];
    } 
}
function decided(match){
    return isFinite(1) && !isNaN(parseFloat(match['home']['score'])) && isFinite(1) && !isNaN(parseFloat(match['away']['score']));
}

export function listMoreMatches(matches, teams){
    let output = '';
    for(var i = 0; i < matches.length; i++){
        let match = matches[i];
        console.log(match["away"]["id"]);
        output +=`<div class="match-content" style="border: 1px solid green;">
            <div class="column">
                <div class="team-de team--home">
                    <div class="team-logo">
                        <img src="` + teams[match["home"]["id"]]["logo"] + `" />
                    </div>
                    <h2 class="team-name">` +  getTeamName(match["home"]["id"], teams)  + `</h2>
                </div>
            </div>
            <div class="column">
                <div class="match-details">
                    <div class="match-date">
                        ` + match["date"] + `
                    </div>
                    <div class="match-score">`;
                        if(decided(match)){
                            output +=`
                            home <span class="match-score-number match-score-number--leading">` + match["home"]["score"] + `</span>
                            <span class="match-score-divider">:</span>
                            <span class="match-score-number">` +  match["away"]["score"] + `</span> away
                            `;
                        } else{
                            output +=`
                            home <span class="match-score-number match-score-number--leading">◻</span>
                            <span class="match-score-divider">:</span>
                            <span class="match-score-number">◻</span> away
                            `;
                        }
                        output += `
                    </div>
                </div>
            </div>
            <div class="column">
                <div class="team-de team--away">
                    <div class="team-logo">
                        <img src="` + teams[match["away"]["id"]]["logo"] +`" />
                    </div>
                    <h2 class="team-name">`+ getTeamName(match["away"]["id"], teams) + ` </h2>
                </div>
            </div>
        </div>
        `;
    };
    return output;
}