//获取项目列表
function showlistJs(){
//ajax获取该用户项目
	$.ajax({
		type: "get",
		dataType: 'json',
		url: JSV.PATH_SERVER+"home/Item/listItem",
		success: function(data){
			//判断该用户是否有项目
			if(data.data){
				var projects;
				$.each(data.data, function() {//存在项目，将项目遍历
					projects = $("#personProject").html();
					projects = projects.replace(/srcImg/,this.img);
					projects = projects.replace(/personProId/,this.id);
					projects = projects.replace(/projectName/,this.i_name);
					$(".productShow_b").append(projects);
				});
				$(".project").css("display","block");
				$(".noProject").css("display","none");
			}else{
				$(".noProject").text("该用户暂时没有项目");
			}
		}
	});
}

//获取项目详情
function lq_projectInfoJs(){
//	alert(prejectId);
	layui.use('layer',function(){
		var layer = layui.layer;
	});
		var index1 = null
	//点击增加日志按钮弹出增加内容弹层
	$(".add_log").click(function(){
		$(".log_name").text(indexUserName);
		$(".log_content").val("");
		index1 = layer.open({
			type: 1,
			closeBtn: 1,
			title: '请添加项目日志内容',
			scroller: false,
			area: ['40%' , '320px'],
			shadeClose: true,
			content: $(".add_log_box")
		});
	});
		
	//获取项目页面数据信息
	$.ajax({
		type: "get",
		dataType: "json",
		data: {
			id: prejectId
		},
		url: JSV.PATH_SERVER+"home/Item/getItem",
		async: false,
		success: function(data){
			
		},
		error: function(){
			alert("error")
		}
	});
	
	//获取当前用户Id,姓名
	var indexUserId;
	var indexUserName;
	$.ajax({
		type: "get",
		dataType:"json",
		url: JSV.PATH_SERVER+"home/Item/getLoginUser",
		async: false,
		success: function(data){
			indexUserId = data.data.id;
			indexUserName = data.data.nickname;
		}
	});

	//获取项目成员信息，姓名，头像
	$.ajax({
		type: "get",
		dataType: "json",
		data: {
			id: prejectId
		},
		url: JSV.PATH_SERVER+"home/Item/getUserInfo",
		async: false,
		success: function(data){
			if(data.data){
				var userInfos;
				$.each(data.data, function() {
					userInfos = $("#projectUserInfos").html();
					userInfos = userInfos.replace(/srcphoto/,this.photo);
					userInfos = userInfos.replace(/projectUserName/,this.nickname);
					userInfos = userInfos.replace(/userTel/,this.tel);
					userInfos = userInfos.replace(/userId/,this.id);
					$(".pro_members").append(userInfos);
				});
			}
		}
	});
	
	var index1 = null;
	//获取项目成员每个div，点击弹出发送消息层
	$('.pro_photo').on('click',function(){
		var proUserTel = $(this).next().attr("data-tel");
		$(".pepole").attr("data-tell",proUserTel);
		var proMemberId = $(this).next().attr("data-id");
		if(proMemberId == indexUserId){
			alert("不能给自己发送消息！")
			return false;
		};
		$(".info_box_text").val("");
		$(".pepole").text($(this).next().text());
		index1 = layer.open({
			type: 1,
			closeBtn: 1,
			title: '发送消息',
			area: ["35%" , "300px"],
			shadeClose: true,
			content: $('.info_box')
		})
	});
	
	//发送消息
	$(".send_btn").on('click',function(){
		//获取接受人的信息
		var acceptUserTel = $(".pepole").attr("data-tell");
		//获取发送 内容
		var sendMsgText = $(".info_box_text").val();
		//判断当发送信息不为空时，执行函数
		if(sendMsgText){	
			$.ajax({
				type: "get",
				url: JSV.PATH_SERVER+"main/Msg/sendMessage",
				data: {
					"receiver": acceptUserTel,
					"content": sendMsgText
				},
				dataType: "json",
				beforeSend: function(){
					var index = layer.load(1,{
						shade: [0.1,"#fff"]
					});
					setTimeout(function(){
						layer.close(index)
					},400)
				},
				success: function(data){
					//消息发送成功弹出层
					setTimeout(function(){
						layer.msg("消息发送成功！")
					},200);
				},
				error: function(XMLHttpRequest, textStatus, errorThrown){
					console.log(XMLHttpRequest.readyState)
                    console.log(XMLHttpRequest.status)
				}
			});
			//消息发送成功，关闭弹出层
			setTimeout(function(){
				layer.close(index1)
			},800);
		};
	});
	
	//获取日志
	$(".submit_btn").click(function(){
		//获取日志内容
		var logContent = $(".log_content").val();
		$.ajax({
			type: "get",
			data:{
				id: prejectId,
				"content": logContent
			},
			dataType: "json",
			url: JSV.PATH_SERVER+"home/Item/sendLog",
//			async:true
			success: function(){
				alert("日志提交成功！");
				layer.close(index1);
			}
		});
	});
	
	//声明变量，日志的时间
	var newLogTime;
	//显示所有日志
	$.ajax({
		type: "get",
		dataType: "json",
		data: {
			id: prejectId
		},
		url: JSV.PATH_SERVER+"home/Item/listItemLog",
		success: function(data){
			if(data.data){
				var newLog;
				$.each(data.data, function() {
					newLogTime = this.Date;
					newLog = $("#addLog").html();
					newLog = newLog.replace(/logName/,this.username);
					newLog = newLog.replace(/logContent/,this.content);
					newLog = newLog.replace(/logtTime/,this.Date);
					$(".item_user_log").append(newLog);
				});
			}
		}
	});
	
	//每隔5秒时间，如果有新的日志增加就添加进来
	setInterval(function(){
		$(".item_user_log").html("");
		$.ajax({
			type: "get",
			dataType: "json",
			async: false,
			data: {
				id: prejectId
			},
			url: JSV.PATH_SERVER+"home/Item/listItemLog",
			success: function(data){
				if(data.data){
					var newAddLog;
					$.each(data.data, function() {
						if((this.Date) != (newLogTime)){
							newAddLog = $("#addLog").html();
							newAddLog = newAddLog.replace(/logName/,this.username);
							newAddLog = newAddLog.replace(/logContent/,this.content);
							newAddLog = newAddLog.replace(/logtTime/,this.Date);
							$(".item_user_log").prepend(newAddLog);
						}
					});
				}
			}
		});
	},5000)
	
}

$(document).ready(function(){
	layui.use('layer',function(){
		var layer = layui.layer;
	});
	
	//获取当前用户tel
	var indexUsertel;
	$.ajax({
		type: "get",
		dataType:"json",
		url: JSV.PATH_SERVER+"home/Item/getLoginUser",
		async: false,
		success: function(data){
			indexUsertel = data.data.tel;
		}
	});
	
	//当前未读消息的数量
	var newMsgNum;//声明消息数量变量
	var newMsgId;//声明消息ID变量
	$.ajax({
		type: "get",
		dataType: "json",
		url: JSV.PATH_SERVER+"main/Msg/unreadMessage",
//		async: false,
		success: function(data){
			newMsgNum = data.data.parentNum;
			$(".newMsg").text(newMsgNum);
			$.each(data.data.parentMsgs, function() {
				newMsgId = this.msg_id;
			});
		}
	});
	
	//每隔5秒刷新后有新消息，获取未读消息
	setInterval(function(){
		$(".newMsg").text("0")
		$.ajax({
			type: "get",
			dataType: "json",
			url: JSV.PATH_SERVER+"main/Msg/unreadMessage",
			async: false,
			success: function(data){
				$.each(data.data.parentMsgs, function() {
						if((this.msg_id) != (newMsgId)){
							$(".newMsg").text(this.msg_id.length)
						}
				});
			}
		});
	},5000);
	
	//点击新消息按钮，弹出层，显示新消息
	$(".newMsg").click(function(){
		$(".newMsg").text("0");
		//ajax获取消息列表
		$.ajax({
			type: "get",
			url: JSV.PATH_SERVER+"main/Msg/getMsgList",
			dataType: "json",
			success: function(data){
				if(data.data){
					var MsgList;
					$.each(data.data, function() {
						var acceptTel = this.rec_user.user_account;
						if(acceptTel == indexUsertel){
							MsgList = $(".acceptMsgList").html();
							MsgList = MsgList.replace(/sendPhoto/,this.send_user.photo);
							MsgList = MsgList.replace(/sendName/,this.send_user.nickname);
							MsgList = MsgList.replace(/sendTime/,this.send_time);
							MsgList = MsgList.replace(/sendContent/,this.content);
							MsgList = MsgList.replace(/sUserTel/,this.send_user.user_account);
							MsgList = MsgList.replace(/sUserTel/,this.msg_id);
							$(".acceptMsgBox").append(MsgList);
						}
					});
				}
			}
		});
		layer.open({
			type: 1,
			closeBtn: 1,
			title: "消息列表",
			area: ["50%" , "500px"],
			shadeClose: true,
			content: $(".acceptMsgBox"),
			end:function(){
				$(".acceptMsgBox").html("");
			}
		});
	});
		
});

//点击回复按钮，显示回复信息框，发送信息按钮
function reply(rMsg){
	rMsg.next().css("display","block");
	rMsg.parent().prev().find(".replyMsg").css("display","block");
	rMsg.css("display","none");
};

//点击发送按钮，发送回复消息
function sendMsg(send){
	layui.use('layer',function(){
		var layer = layui.layer;
	});
	//获取发送的回复消息
	var sMsg = send.parent().prev().find(".replyMsg").val();
	//获取接受回复消息的账号
	var aa = send.attr("data-stel")
//	console.log(aa)
	//发送消息，判断当消息不为空时，发送
	if(sMsg){
		$.ajax({
			type: "get",
			url: JSV.PATH_SERVER+"main/Msg/sendMessage",
			data: {
				"receiver": aa,
				"content": sMsg
			},
			dataType: "json",
			success: function(){
				send.parent().parent().remove();
				//消息发送成功弹出层
				setTimeout(function(){
					layer.msg("消息发送成功！")
				},200);
			}
		});
	}
}
