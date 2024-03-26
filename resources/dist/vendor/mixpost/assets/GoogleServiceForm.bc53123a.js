import{S as v,H as h,o as x,k as $,w as e,e as s,a as o,t as n,b as m,h as f,_ as V,ac as k,J as b}from"./app.a05b4c5c.js";import{_ as w}from"./Panel.05e9bba5.js";import{_ as y}from"./Input.44809a08.js";import{G}from"./Services.fcee3950.js";import{_ as p}from"./HorizontalGroup.97d4c34e.js";import{_}from"./Error.4aa59f2e.js";import{_ as C}from"./ReadDocHelp.faafaa35.js";import{_ as S}from"./InputHidden.3ebb63fe.js";import"./Admin.7fa4ba78.js";import"./PageHeader.044c6731.js";import"./Flex.513e8e8d.js";import"./Tab.af6a2b17.js";import"./ProviderIcon.9a25a410.js";import"./PureButton.7cf07808.js";import"./Eye.bdca2e42.js";const N={class:"flex items-center"},O={class:"mr-xs"},B=s("span",null,"Google",-1),I={href:"https://console.developers.google.com/",class:"link",target:"_blank"},D=s("label",{for:"id"},"Client ID",-1),P=s("label",{for:"secret"},"Client secret",-1),W={__name:"GoogleServiceForm",props:{form:{required:!0,type:Object}},setup(r){const u=r,{t:a}=v(),{notify:d}=b(),t=h({}),g=()=>{t.value={},k.put(route("mixpost.services.update",{service:"google"}),u.form,{preserveScroll:!0,onSuccess(){d("success",a("service.service_saved",{service:"Google"}))},onError:i=>{t.value=i}})};return(i,l)=>(x(),$(w,{class:"mt-lg"},{title:e(()=>[s("div",N,[s("span",O,[o(G,{class:"text-google"})]),B])]),description:e(()=>[s("p",null,[s("a",I,n(m(a)("service.create_app",{name:"Google Console"})),1),f(". ")]),o(C,{href:`${i.$page.props.mixpost.docs_link}/books/integration-of-social-platforms/page/google`,class:"mt-xs"},null,8,["href"])]),default:e(()=>[o(p,{class:"mt-lg"},{title:e(()=>[D]),footer:e(()=>[o(_,{message:t.value.client_id},null,8,["message"])]),default:e(()=>[o(y,{modelValue:r.form.client_id,"onUpdate:modelValue":l[0]||(l[0]=c=>r.form.client_id=c),error:t.value.hasOwnProperty("client_id"),type:"text",id:"id",autocomplete:"off"},null,8,["modelValue","error"])]),_:1}),o(p,{class:"mt-lg"},{title:e(()=>[P]),footer:e(()=>[o(_,{message:t.value.client_secret},null,8,["message"])]),default:e(()=>[o(S,{modelValue:r.form.client_secret,"onUpdate:modelValue":l[1]||(l[1]=c=>r.form.client_secret=c),error:t.value.hasOwnProperty("client_secret"),id:"secret",autocomplete:"new-password"},null,8,["modelValue","error"])]),_:1}),o(V,{onClick:g,class:"mt-lg"},{default:e(()=>[f(n(m(a)("general.save")),1)]),_:1})]),_:1}))}};export{W as default};
