import{O as _,o as t,c as a,e as b,F as r,a2 as g,E as p,k as n,P as u,h as m,t as i,w as h,b as d,q as v}from"./app.a05b4c5c.js";import{C,a as w}from"./ChevronLeft.727e7cc4.js";const N={class:"bg-white border border-gray-100 rounded-lg p-sm w-fit"},B={class:"flex flex-wrap items-center space-x-1"},L={__name:"Pagination",props:{meta:{type:Object,default:{current_page:1,from:1,last_page:1,per_page:2,to:1,total:0,links:[]}},links:{type:Object,default:{first:null,last:null,next:null,prev:null}}},setup(x){const f=x,l="px-sm py-xs rounded-md leading-4",y=_(()=>f.meta.links.map(s=>{const o=s.label.replace("&laquo; ","").replace(" &raquo;","");return{active:s.active,url:s.url,label:o,component:isNaN(parseInt(s.label))?{Next:C,Previous:w}[o]:null}}));return(s,o)=>(t(),a("div",N,[b("div",B,[(t(!0),a(r,null,g(d(y),(e,c)=>(t(),a(r,null,[e.url===null?(t(),a("div",{key:c,class:p([[l,{"!px-0":e.label==="...","!px-xs":e.component}],"text-gray-400"])},[e.component?(t(),n(u(e.component),{key:0})):(t(),a(r,{key:1},[m(i(e.label),1)],64))],2)):(t(),n(d(v),{key:`link-${c}`,disabled:"",class:p(["transition-colors ease-in-out duration-200",[l,{"bg-primary-500 text-white":e.active,"hover:text-primary-500 focus:text-primary-500":!e.active,"!px-xs":e.component}]]),href:e.url},{default:h(()=>[e.component?(t(),n(u(e.component),{key:0})):(t(),a(r,{key:1},[m(i(e.label),1)],64))]),_:2},1032,["class","href"]))],64))),256))])]))}};export{L as _};
