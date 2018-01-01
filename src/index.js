import React from 'react';
import ReactDOM from 'react-dom';
import {Link} from 'react-router';
import axios from 'axios';
import './index.css';
import $ from 'jquery'; 
import registerServiceWorker from './registerServiceWorker';

const php = 'http://localhost/algo/src/php/';

class Subscriptions extends React.Component {
	constructor(props) {
		super(props);
		this.state = {msg: '', subscriptions: new Map(), roasters: new Map()};
	}

	onNewRoaster(msg, data) {
		this.setState({msg: msg});
		let roasters = this.state.roasters;
		roasters.set(data.name, 0);
		this.setState({roasters: roasters});
	}

	onNewSubscription(msg, data) {
		this.setState({msg: msg});
		let subscriptions = this.state.subscriptions;
		subscriptions.set(data.id, data.detail);
		this.setState({subscriptions: subscriptions});
		this.updateRoasterOrders(data.detail.roasters);
	}

	updateRoasterOrders(name) {
		let roasters = this.state.roasters;
		let cnt = roasters.get(name);
		roasters.set(name, cnt+1);
		this.setState({roasters: roasters});
	}

	onNextOrder(name, id) {
		let subscriptions = this.state.subscriptions;
		let detail = subscriptions.get(id);
		detail.roasters = detail.roasters + ", " + name;
		subscriptions[id] = detail;
		this.setState({subscriptions: subscriptions});
		this.updateRoasterOrders(name);
	}

	onLoad() {
		$.ajax({
		    url: php + 'subscriptions.php',
		    type: 'POST',
		    crossDomain: true,
		    success: function(data) {
		    	data = JSON.parse(data);
		    	let subscriptions = new Map();
		    	$.each(data, function(key, val) {
		    		subscriptions.set(key, val);
		    	});
		    	this.setState({subscriptions: subscriptions});
		    }.bind(this),
		    error: function() { alert('Failed!'); }.bind(this),
		});

		$.ajax({
		    url: php + 'roaster-orders.php',
		    type: 'POST',
		    crossDomain: true,
		    success: function(data) {
		    	data = JSON.parse(data);
		    	let roasters = new Map();
		    	$.each(data, function(key, val) {
		    		roasters.set(key, val);
		    	});
		    	this.setState({roasters: roasters});
		    }.bind(this),
		    error: function() { alert('Failed!'); }.bind(this),
		});
	}

	componentDidMount() {
	    window.addEventListener('load', this.onLoad.bind(this));
	}

	render() {
		let subscriptions = this.state.subscriptions;
		let roasters = this.state.roasters;
		let orders = [];
		for( let [key, val] of subscriptions )
			orders.push(<SubscriptionDetail id={key} name={val.name} key={key} roasters={val.roasters} onOrder={this.onNextOrder.bind(this)} />);

		let roaster = [];
		for( let [key, val] of roasters )
			roaster.push(<RoasterDetail name={key} key={key} orders={val} />);

		return (
			<div>
				<h4>{this.state.msg}</h4>
				<div className="roaster">
					{roaster}
				</div>
				<div className="add-new">
					<AddRoaster onSuccess={this.onNewRoaster.bind(this)} />
					<AddSubscription onSuccess={this.onNewSubscription.bind(this)} />
				</div>
				{orders}
			</div>
		);
	}
}

function RoasterDetail(props) {
	return (
		<div>{props.name} ({props.orders})</div>
	);
}

class SubscriptionDetail extends React.Component {
	constructor(props) {
		super(props);
	}

	HandleNewOrder(e) {
		let id = e.target.id;
		$.ajax({
		    url: php + 'newOrder.php',
		    type: 'POST',
		    data: {id: id},
		    crossDomain: true,
		    success: function(name) {
		    	this.props.onOrder(name, id);
		    }.bind(this),
		    error: function() { alert('Failed!'); }.bind(this),
		});
	}

	render() {
		return (
			<div className="subscription-detail">
				<h4>
					{this.props.name}
					&nbsp;&nbsp;
					<input type="button" value="Place Order" id={this.props.id} onClick={this.HandleNewOrder.bind(this)} />
				</h4>
				<h5>{this.props.roasters}</h5>
			</div>
		);
	}
}

class AddSubscription extends React.Component {
	constructor(props) {
		super(props);
	}

	HandleClick(e) {
		$.ajax({
		    url: php + 'addSubscription.php',
		    type: 'POST',
		    crossDomain: true,
		    success: function(data) {
		    	data = JSON.parse(data);
		    	this.props.onSuccess('Subscription added !!!', data);
		    }.bind(this),
		    error: function() { alert('Failed!'); }.bind(this),
		});
	}

	render() {
		return (
			<div>
				<input type="button" value="New Subscription" onClick={this.HandleClick.bind(this)} />
			</div>
		);
	}
}

class AddRoaster extends React.Component {
	constructor(props) {
		super(props);
	}

	HandleFormSubmit(e) {
		e.preventDefault();
		let name = e.target.roaster.value;
		$.ajax({
		    url: php + 'addRoaster.php',
		    data: {name: name},
		    type: 'POST',
		    crossDomain: true,
		    success: function(data) {
		    	data = JSON.parse(data);
		    	this.refs.form.reset();
		    	this.props.onSuccess('Roaster added !!!', data);
		    }.bind(this),
		    error: function() { alert('Failed!'); }.bind(this),
		});
	}

	render() {
		return (
			<div>
				<form onSubmit={this.HandleFormSubmit.bind(this)} ref="form">
					<label htmlFor="roaster">New Roaster</label>
					<input type="text" name="roaster" required />
					<input type="submit" value="Add" />
				</form>
			</div>
		);
	}
}

ReactDOM.render(
	<Subscriptions />,
	document.getElementById('root')
);
registerServiceWorker();