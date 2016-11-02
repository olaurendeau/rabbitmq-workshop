class App extends React.Component {
    state = { email : "john.doe@foobar.com", messages: [] }
    send = (event) =>  {
        // Prepare request
        let request = {
            method: "createDocument",
            id: guid(),
            params: {
                email: this.state.email
            }
        };

        // Inform user
        this.log({type: "request", id: request.id, message: <span>Invoice request sent, please wait for a while ...</span>});

        // Send request
        $.post('http://localhost:4445/api.php', JSON.stringify(request), (response) => {
            // Display result
            this.log({type: "response", id: response.id, message: <span>Your invoice have been generated and sent by email !,&nbsp;<a target="_blank" href="http://localhost:8025/">check email</a></span>
            });
        }, "json").fail((response) => {
            // Display error
            this.log({type: "response", id: response.responseJSON.id, message: <span>Failure : {response.responseJSON.error.message}</span>});
        });
        event.preventDefault(); event.stopPropagation();
    }
    log = (message) => {
        this.setState({messages: [...this.state.messages, ...[message]]});
    }
    emailChanged = (event) => {
        this.setState({email: event.target.value})
    }
    render() {
        return <form onSubmit={this.send}>
            <h2>Send invoice</h2>
            <div>
                <label>Email <input type="text" onChange={this.emailChanged} defaultValue={this.state.email}/> </label><br/><br/>
                <input type="submit" value="Send!"/>
            </div>
            <h2>Result</h2>
            <div style={{fontFamily: "monospace"}}>
                {this.state.messages.map((message) => <div key={message.type+"-"+message.id}>
                    <span style={{backgroundColor: "#"+message.id.substr(0,6), color: "white"}}>#{message.id}</span>&nbsp;
                    {message.message}
                </div>)}
            </div>
        </form>
    }
}

ReactDOM.render(<App/>, document.getElementById("container"));
