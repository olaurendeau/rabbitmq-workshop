class App extends React.Component {
    state = { name : null, messages: [] }
    send = () =>  {
        let request = {
            method: "createDocument",
            id: guid(),
            params: {
                name: this.state.name
            }
        };
        this.log({id: request.id, message: <span>Invoice request sent, please wait for a while ...</span>});
        $.post('http://localhost:4445/api.php', JSON.stringify(request), (response) => {
            this.log({id: response.id, message: <span>Your invoice have been generated, <a target="_blank" href={response.result}>download it</a></span>});
        }, "json").fail((response) => {
            this.log({id: response.id, message: <span>Failure</span>});
        });
    }
    log = (message) => {
        this.setState({messages: [...this.state.messages, ...[message]]});
    }
    onChange = (event) => {
        this.setState({name: event.target.value})
    }
    render() {
        return <div>
            <label>Download invoice for order n° <input type="text" onChange={this.onChange}/> </label>
            <input onClick={this.send} type="button" value="Submit"/>
            {this.state.messages.map((message) => <div>
                <span style={{backgroundColor: "#"+message.id.substr(0,6), color: "white"}}>#{message.id}</span>&nbsp;
                {message.message}
            </div>)}
        </div>
    }
}

ReactDOM.render(<App/>, document.getElementById("container"));
