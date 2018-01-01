import React from 'react';
import ReactDOM from 'react-dom';
import {Link} from 'react-router';
import axios from 'axios';
import './index.css';
import $ from 'jquery'; 
import registerServiceWorker from './registerServiceWorker';

const php = 'http://localhost/algo/src/php/';

class Roasters extends React.Component {
	constructor(props) {
		super(props);
		this.state = {subscriptions: []};
		this.HandlePlacedOrder = this.HandlePlacedOrder.bind(this);
		this.handleLoad = this.handleLoad.bind(this);
	}

	HandlePlacedOrder(id) {
		// console.log(id);
		$.ajax({
		    url: php + 'select-roaster.php',
		    data: {id: id},
		    type: 'POST',
		    crossDomain: true,
		    // dataType: 'jsonp',
		    success: function(data) {
		    	let subscriptions = this.state.subscriptions;
		    	subscriptions[Number(id)-1] += data + ", ";
		    	this.setState({subscriptions: subscriptions});
		    }.bind(this),
		    error: function() { alert('Failed!'); }.bind(this),
		});
	}

	componentDidMount() {
	    window.addEventListener('load', this.handleLoad);
	}

	handleLoad() {
		let subscriptions = this.state.subscriptions;
		let len = subscriptions.length;
		// console.log(subscriptions.length);
		for( let i = 1; i <= len; i++ )
		{
			$.ajax({
			    url: php + 'subscription.php',
			    data: {id: i},
			    type: 'POST',
			    crossDomain: true,
			    // dataType: 'jsonp',
			    success: function(data) {
			    	subscriptions[i-1] = data;
			    	this.setState({subscriptions: subscriptions});
			    }.bind(this),
			    error: function() { alert('Failed!'); }.bind(this),
			});
		}
	}

	render() {
		return (
			<div>
				<Subscription id='1' onOrder={this.HandlePlacedOrder} roasters={this.state.subscriptions[0]} />
				<Subscription id='2' onOrder={this.HandlePlacedOrder} roasters={this.state.subscriptions[1]} />
			</div>
		);
	}
}

class Subscription extends React.Component {
	constructor(props) {
		super(props);
		this.HandleClick = this.HandleClick.bind(this);
	}

	HandleClick(e) {
		let id = e.target.id;
		this.props.onOrder(id);
	}

	render() {
		return (
			<div className='roaster-div'>
				<h4>Subscription {this.props.id}</h4>
				<button id={this.props.id} onClick={this.HandleClick}>Order</button>
				<br />
				<span>{this.props.roasters}</span>
			</div>
		);
	}
}

ReactDOM.render(
	<Roasters />,
	document.getElementById('root')
);
registerServiceWorker();