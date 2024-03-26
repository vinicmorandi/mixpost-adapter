import{S as L,n as I,W as X,H as M,r as R,o as l,c as h,e as b,j,b as s,k as w,w as t,a as e,a7 as q,ab as Y,h as m,t as d,a8 as ee,ac as S,c5 as O,J as z,O as W,l as D,A as te,c8 as A,F as P,a2 as H,E as se,f as y,d as oe,u as ae,a0 as re,a5 as le,a6 as B,Z as ne,q as ce,_ as ie,au as E}from"./app.a05b4c5c.js";import{u as ue}from"./useSelectable.08fa8b7e.js";import{_ as me}from"./Admin.7fa4ba78.js";import{_ as de}from"./PageHeader.044c6731.js";import{_ as pe}from"./PureDangerButton.9adab555.js";import{_ as fe}from"./Panel.05e9bba5.js";import{_ as F}from"./Checkbox.12b1be22.js";import{_ as c,a as J,T as _e}from"./TableCell.8aafd44e.js";import{_ as ke}from"./SelectableBar.c8863131.js";import{_ as we}from"./NoResult.b8d4ef7e.js";import{T as Z}from"./Trash.83ab4124.js";import{_ as he}from"./Pagination.cc249589.js";import{_ as N}from"./PureButtonLink.21473f96.js";import{E as ge}from"./Eye.bdca2e42.js";import{_ as $e}from"./DropdownButton.8d634dd9.js";import{A as ve}from"./ArrowTopRightOnSquare.a67b2cfb.js";import{_ as be}from"./SearchInput.296daeed.js";import{_ as ye}from"./Alert.49de0ead.js";import{_ as xe}from"./Flex.513e8e8d.js";import"./ChevronLeft.727e7cc4.js";import"./PureButton.7cf07808.js";import"./EllipsisVertical.0bda3946.js";import"./Input.44809a08.js";const Ce={class:"flex flex-row items-center justify-end gap-xs"},Ve={__name:"WorkspaceItemAction",props:{itemId:{type:String,required:!0}},emits:["onDelete"],setup(o,{emit:p}){const a=o,{t:r}=L(),i=I("routePrefix"),x=I("confirmation"),{notify:$}=z();X();const _=M(!1),f=()=>{x().title(r("workspace.delete_workspace")).description(r("workspace.confirm_delete_workspace")).destructive().onConfirm(u=>{u.isLoading(!0),S.delete(route(`${i}.workspaces.delete`,{workspace:a.itemId}),{onSuccess(){_.value=!1,$("success",r("workspace.workspace_deleted")),p("onDelete"),O.emit("workspaceDelete",a.itemId),u.reset()},onFinish(){u.isLoading(!1)}})}).show()};return(u,C)=>{const v=R("tooltip");return l(),h("div",null,[b("div",Ce,[j((l(),w(N,{href:u.route("mixpost.switchWorkspace",{workspace:o.itemId,redirect:!0}),method:"post",as:"button",type:"button"},{default:t(()=>[e(ve)]),_:1},8,["href"])),[[v,s(r)("general.open")]]),j((l(),w(N,{href:u.route(`${s(i)}.workspaces.view`,{workspace:o.itemId})},{default:t(()=>[e(ge)]),_:1},8,["href"])),[[v,s(r)("general.view")]]),e(ee,{"width-classes":"w-32",placement:"bottom-end"},{trigger:t(()=>[e($e)]),content:t(()=>[e(q,{href:u.route(`${s(i)}.workspaces.edit`,{workspace:o.itemId})},{default:t(()=>[e(Y,{class:"mr-xs"}),m(" "+d(s(r)("general.edit")),1)]),_:1},8,["href"]),e(q,{onClick:f,as:"button"},{default:t(()=>[e(Z,{class:"text-red-500 mr-xs"}),m(" "+d(s(r)("general.delete")),1)]),_:1})]),_:1})])])}}},De={key:0,class:"flex justify-start"},Ie={key:0,class:"mt-xs"},je={__name:"WorkspaceItem",props:{item:{type:Object,required:!0}},setup(o){const p=o,a=W(()=>D.exports.sortBy(p.item.users.slice(0,3),"pivot.joined_at")),r=W(()=>p.item.users.slice(3).length);return(i,x)=>{const $=R("tooltip");return l(),w(J,{hoverable:!0},{default:t(()=>[e(c,{class:"w-10"},{default:t(()=>[te(i.$slots,"checkbox")]),_:3}),e(c,null,{default:t(()=>[e(A,{backgroundColor:o.item.hex_color,name:o.item.name,roundedClass:"rounded-lg"},null,8,["backgroundColor","name"])]),_:1}),e(c,null,{default:t(()=>[b("div",null,d(o.item.name),1)]),_:1}),e(c,null,{default:t(()=>[m(d(o.item.created_at),1)]),_:1}),e(c,null,{default:t(()=>[o.item.users.length?(l(),h("div",De,[(l(!0),h(P,null,H(s(a),(_,f)=>j((l(),w(A,{key:_.id,name:_.name,class:se([{"-ml-6":f>0},"cursor-default mr-xs last:mr-xs"])},null,8,["name","class"])),[[$,_.name]])),128)),s(r)?(l(),h("div",Ie," +"+d(s(r)),1)):y("",!0)])):y("",!0)]),_:1}),e(c,null,{default:t(()=>[e(Ve,{itemId:o.item.uuid},null,8,["itemId"])]),_:1})]),_:3})}}},Se={class:"flex items-center"},Oe={__name:"Filters",props:{modelValue:{type:Object,required:!0}},emits:["update:modelValue"],setup(o,{emit:p}){return W(()=>0),(a,r)=>(l(),h("div",Se,[e(be,{modelValue:o.modelValue.keyword,"onUpdate:modelValue":r[0]||(r[0]=i=>o.modelValue.keyword=i),placeholder:a.$t("general.search")},null,8,["modelValue","placeholder"])]))}},We={class:"w-full mx-auto max-w-6xl row-py"},Pe={class:"mt-lg row-px w-full"},Re=["href"],Te={key:1,class:"mt-lg"},Ue=oe({layout:me}),lt=Object.assign(Ue,{__name:"Workspaces",props:{workspaces:{type:Object},filter:{type:Object,default:{}}},setup(o){const p=o,{t:a}=L(),r=I("routePrefix"),i=a("workspace.workspaces"),x=I("confirmation"),{notify:$}=z(),{enterpriseConsole:_}=ae(),{selectedRecords:f,putPageRecords:u,toggleSelectRecordsOnPage:C,deselectRecord:v,deselectAllRecords:T}=ue(),U=()=>p.workspaces.data.map(n=>n.uuid),V=M({keyword:p.filter.keyword});re(()=>{u(U()),O.on("workspaceDelete",n=>{v(n)})}),le(()=>{O.off("workspaceDelete")}),B(()=>p.workspaces.data,()=>{u(U())}),B(()=>D.exports.cloneDeep(V.value),D.exports.throttle(()=>{S.get(route(`${r}.workspaces.index`),D.exports.pickBy(V.value),{preserveState:!0,only:["workspaces","filter"]})},300));const G=()=>{x().title(a("workspace.delete_workspaces")).description(a("workspace.confirm_delete_workspaces")).destructive().onConfirm(n=>{n.isLoading(!0),S.delete(route(`${r}.workspaces.multipleDelete`),{data:{workspaces:f.value},preserveScroll:!0,onSuccess(){T(),$("success",a("workspace.workspaces_deleted"))},onFinish(){n.reset()}})}).show()};return(n,g)=>{const K=R("tooltip");return l(),h(P,null,[e(s(ne),{title:s(i)},null,8,["title"]),b("div",We,[e(de,{title:s(i)},{description:t(()=>[m(d(s(a)("workspace.manage_brands_businesses")),1)]),_:1},8,["title"]),b("div",Pe,[e(ke,{count:s(f).length,onClose:s(T)},{default:t(()=>[j((l(),w(pe,{onClick:G},{default:t(()=>[e(Z)]),_:1})),[[K,s(a)("general.delete")]])]),_:1},8,["count","onClose"]),s(_).url?(l(),w(ye,{key:0,variant:"warning",closeable:!1,class:"mb-lg"},{default:t(()=>[m(" Manage Workspaces from the "),b("a",{href:`${s(_).url}/workspaces`,class:"link"},"Enterprise Console",8,Re),m(" for more business actions. ")]),_:1})):y("",!0),e(xe,{class:"justify-between"},{default:t(()=>[e(s(ce),{href:n.route(`${s(r)}.workspaces.create`),class:"mb-xs sm:mb-0"},{default:t(()=>[e(ie,null,{default:t(()=>[m(d(s(a)("workspace.create_workspace")),1)]),_:1})]),_:1},8,["href"]),e(Oe,{modelValue:V.value,"onUpdate:modelValue":g[0]||(g[0]=k=>V.value=k)},null,8,["modelValue"])]),_:1}),e(fe,{"with-padding":!1,class:"mt-lg"},{default:t(()=>[e(_e,null,{head:t(()=>[e(J,null,{default:t(()=>[e(c,{component:"th",scope:"col",class:"w-10"},{default:t(()=>[e(F,{checked:s(C),"onUpdate:checked":g[1]||(g[1]=k=>E(C)?C.value=k:null),disabled:!n.$page.props.workspaces.meta.total},null,8,["checked","disabled"])]),_:1}),e(c,{component:"th",scope:"col"}),e(c,{component:"th",scope:"col"},{default:t(()=>[m(d(s(a)("general.name")),1)]),_:1}),e(c,{component:"th",scope:"col"},{default:t(()=>[m(d(s(a)("general.created_at")),1)]),_:1}),e(c,{component:"th",scope:"col"},{default:t(()=>[m(d(s(a)("user.users")),1)]),_:1}),e(c,{component:"th",scope:"col"})]),_:1})]),body:t(()=>[(l(!0),h(P,null,H(n.$page.props.workspaces.data,k=>(l(),w(je,{key:k.uuid,item:k,onOnDelete:()=>{s(v)(k.uuid)}},{checkbox:t(()=>[e(F,{checked:s(f),"onUpdate:checked":g[2]||(g[2]=Q=>E(f)?f.value=Q:null),value:k.uuid},null,8,["checked","value"])]),_:2},1032,["item","onOnDelete"]))),128))]),_:1}),n.$page.props.workspaces.meta.total?y("",!0):(l(),w(we,{key:0,table:""}))]),_:1}),n.$page.props.workspaces.meta.links.length>3?(l(),h("div",Te,[e(he,{meta:n.$page.props.workspaces.meta,links:n.$page.props.workspaces.links},null,8,["meta","links"])])):y("",!0)])])],64)}}});export{lt as default};