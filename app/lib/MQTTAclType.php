<?php

enum MQTTAclType: string {
	const publishClientSend = "publishClientSend";
	const publishClientReceive = "publishClientReceive";
	const subscribeLiteral = "subscribeLiteral";
	const subscribePattern = "subscribePattern";
	const unsubscribeLiteral = "unsubscribeLiteral";
	const unsubscribePattern = "unsubscribePattern";
}
