import{H as l,O as R}from"./app.a05b4c5c.js";const g=()=>{const s=l([]),c=l([]),u=R({get(){return n(s.value)},set(){const e=s.value;if(n(e)){f(e);return}a(e)}}),d=e=>{s.value=e},a=e=>{for(const t of e)r(t)||c.value.push(t)},o=e=>{let t=c.value.indexOf(e);t!==-1&&c.value.splice(t,1)},f=e=>{for(const t of e)o(t)},i=()=>{c.value=[]},r=e=>c.value.includes(e),n=e=>e.length?e.every(t=>r(t)):!1;return{selectedRecords:c,toggleSelectRecordsOnPage:u,putPageRecords:d,deselectRecord:o,deselectAllRecords:i}};export{g as u};
