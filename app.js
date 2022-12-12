let titre = document.getElementById("titre");
let artiste = document.getElementById("artiste");
let param1;
let param2;




titre.addEventListener("input", function(){

    param1 = artiste.value;
    param2 = titre.value
const options = {
	method: 'GET',
	headers: {
		'X-RapidAPI-Key': '5a0bb38e4emsh68c798a81b128acp1f9d4bjsn1e92f0153710',
		'X-RapidAPI-Host': 'shazam.p.rapidapi.com'
	}
};

fetch(`https://shazam.p.rapidapi.com/search?term=${param1}&locale=fr-FR&offset=0&limit=5`, options)
	.then(response => response.json())
	.then((data) => {
        console.log(data);
        const result = data.tracks.hits;
        console.log(result);
        result.forEach(element => {
            const title = element.track['title'];
            const artist = element.track['subtitle'];
            
            document.querySelector('#search').innerHTML += `<option value= ${title} par : ${artist} >${title} par : ${artist}</option>`
        })
    })
	.catch(err => console.error(err));})


// let titre = document.getElementById("titre");
// let artiste = document.getElementById("artiste");
// let param1;
// let param2;
// titre.addEventListener("input",() => {
//       param1= titre.value; 
//       param2 = artiste.value; 
//       console.log(param1,param2); 
//       const options = {
//         method: 'GET',
//         headers: {
//             'X-RapidAPI-Key': '5a0bb38e4emsh68c798a81b128acp1f9d4bjsn1e92f0153710',
// 		    'X-RapidAPI-Host': 'shazam.p.rapidapi.com'
//         }
//     };
//     document.querySelector('#search').innerHTML = " ";
//     if(titre.value != ""){
        
//         fetch(`https://shazam.p.rapidapi.com/search?term=${param1}%20${param2}&locale=en-US&offset=0&limit=5`, options)
//         .then(response => response.json())
//         .then((data) => {
//             const result = data.tracks.hits;
//             console.log(result);
//             result.forEach(element => {
//                 const title = element.track['title'];
//                 const artist = element.track['subtitle'];
                
//                 console.log(title)
//                 document.querySelector('#search').innerHTML += `<option value="${title} par${artist}">${title} par ${ artist}</option>`
//             });
//         })
//         .catch(err => console.error(err));
//     }});
   